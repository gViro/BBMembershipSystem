<?php namespace BB\Process;

use Carbon\Carbon;

class CheckMemberships {

    public function run()
    {

        $today = new Carbon();
        $standingOrderCutoff = $today->subMonth()->subDays(7);
        $paypalCutoff = $today->subMonth();
        $otherCutoff = $today->subDays(7);

        $users = \User::active()->get();
        foreach ($users as $user)
        {
            echo $user->name;
            $expired = false;

            if ($user->payment_method == 'gocardless')
            {

            }
            elseif ($user->payment_method == 'standing-order')
            {
                if ($user->subscription_expires->lt($standingOrderCutoff))
                {
                    echo "- S/O Expired";
                    $expired = true;
                }
            }
            elseif ($user->payment_method == 'paypal')
            {
                if ($user->subscription_expires->lt($paypalCutoff))
                {
                    echo "- Paypal Expired";
                    $expired = true;
                }
            }
            else
            {
                if ($user->subscription_expires->lt($otherCutoff))
                {
                    echo "- Other Expired";
                    $expired = true;
                }
            }

            if ($expired)
            {
                $user->status = 'payment-warning';
                //$user->save();
            }


            echo '<br />'.PHP_EOL;
        }
    }
} 