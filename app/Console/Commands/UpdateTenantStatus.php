<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\TenantOverdueMail;
use App\Mail\TenantVoidedMail;

class UpdateTenantStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tenant-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update tenant status based on payment activity and notify via email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = User::where('role', 'tenant')->get();
        $today = Carbon::now();

        foreach ($tenants as $tenant) {
            if (!$tenant->email) continue;

            $statusChanged = false;
            $oldStatus = $tenant->status;

            $hasPaidDeposit = Payment::where('user_id', $tenant->id)
                ->where('payment_for', 'Deposit')
                ->exists();

            $daysSinceCreation = $tenant->created_at->diffInDays(Carbon::today());
            $daysSinceUpdate = $tenant->updated_at->diffInDays(Carbon::today());

            // 1️⃣ Account voided
            if (!$hasPaidDeposit && $daysSinceCreation >= 7) {
                $tenant->status = 'void';
                $tenant->rental_payment_status = 'pending';
                $tenant->utility_payment_status = 'pending';
                $statusChanged = true;

                try {
                    Mail::to($tenant->email)->send(new TenantVoidedMail($tenant));
                    $this->info("📩 Sent voided notice to {$tenant->email}");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to send void email to {$tenant->email}: {$e->getMessage()}");
                }

                $this->info("{$tenant->name} → Account voided.");
                $tenant->save();
                continue;
            }

            // 2️⃣ Overdue payments
            $isOverdue = false;
            if (($tenant->rent_balance > 0 || $tenant->utility_balance > 0) && $daysSinceUpdate > 30) {
                $tenant->rental_payment_status = 'overdue';
                $tenant->utility_payment_status = 'overdue';
                $isOverdue = true;
                $statusChanged = true;

                try {
                    $tenant = User::find(1);
                    Mail::to($tenant->email)->send(new TenantOverdueMail($tenant));
                    $this->info("📩 Sent overdue notice to {$tenant->email}");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to send overdue email to {$tenant->email}: {$e->getMessage()}");
                }
            }

            // 3️⃣ Settled
            if ($tenant->rent_balance == 0 && $tenant->utility_balance == 0) {
                $tenant->rental_payment_status = 'settled';
                $tenant->utility_payment_status = 'settled';
                $statusChanged = true;
            }

            // 4️⃣ Pending
            if (($tenant->rent_balance > 0 || $tenant->utility_balance > 0) && !$isOverdue) {
                $tenant->rental_payment_status = 'pending';
                $tenant->utility_payment_status = 'pending';
                $statusChanged = true;
            }

            if ($statusChanged) $tenant->save();

            if ($statusChanged && $oldStatus !== $tenant->status) {
                $this->info("Updated {$tenant->name}'s status to {$tenant->status}");
            }
        }


        $this->info('✅ Tenant status check complete.');
    }
}
