<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Equipment Inductions</h3>
    </div>
    <div class="panel-body">
        <p>
            We charge a fee for equipment inductions, this fee goes towards the initial purchase cost of the equipment and the ongoing maintenance.<br />
            If you would like to use the equipment below you will need to arrange an induction with a member, the best way of doing this is to post to the mailing list expressing an interest and one or more people should be able to help.
            The fee will need to be paid first but this can be done at any point, if you don't want to pay by Direct Debit you can pay in cash at the space.<br />
            <em>Build Brighton does not provide training but other members will do their best to help you out.</em>
        </p>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Cost</th>
            <th></th>
            <th>
                Inducted By
            </th>
            <th>
                Can Induct Members
            </th>
            <th>
                @if (Auth::user()->isAdmin())
                Payment
                <span class="label label-danger">Admin</span>
                @endif
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($inductions as $itemKey=>$item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>&pound;{{ $item->cost }}</td>
            <td>
                @if ($item->userInduction && ($item->userInduction->is_trained))
                {{ $item->userInduction->trained->toFormattedDateString() }}
                @elseif ($item->userInduction && $item->userInduction->paid)
                Pending
                @else
                {{ Form::open(array('method'=>'POST', 'route' => ['account.payment.create', $user->id])) }}
                {{ Form::hidden('induction_key', $itemKey) }}
                {{ Form::hidden('reason', 'induction') }}
                {{ Form::hidden('source', 'gocardless') }}
                {{ Form::submit('Pay Now (Direct Debit)', array('class'=>'btn btn-primary btn-xs')) }}
                {{ Form::close() }}
                @endif
            </td>
            <td>
                @if (Auth::user()->isAdmin() && $item->userInduction && !$item->userInduction->is_trained)
                {{ Form::open(array('method'=>'PUT', 'route' => ['account.induction.update', $user->id, $item->userInduction->id])) }}
                {{ Form::select('trainer_user_id', \BB\Entities\Induction::trainersForDropdown($itemKey)) }}
                {{ Form::hidden('mark_trained', '1') }}
                {{ Form::submit('Inducted By', array('class'=>'btn btn-default btn-xs')) }}
                {{ Form::close() }}
                @elseif ($item->userInduction && $item->userInduction->is_trained)
                {{ $item->userInduction->trainer_user->name or '' }}
                @endif
            </td>
            <td>
                @if (Auth::user()->isAdmin() && $item->userInduction && $item->userInduction->is_trained && !$item->userInduction->is_trainer)
                {{ Form::open(array('method'=>'PUT', 'route' => ['account.induction.update', $user->id, $item->userInduction->id])) }}
                {{ Form::hidden('is_trainer', '1') }}
                {{ Form::submit('Make a Trainer', array('class'=>'btn btn-default btn-xs')) }}
                {{ Form::close() }}
                @elseif ($item->userInduction && $item->userInduction->is_trained && $item->userInduction->is_trainer)
                Yes
                @endif
            </td>
            <td>
                @if (Auth::user()->isAdmin() && (!$item->userInduction || ($item->userInduction && !$item->userInduction->paid)))
                {{ Form::open(array('method'=>'POST', 'route' => ['account.payment.store', $user->id])) }}
                {{ Form::hidden('induction_key', $itemKey) }}
                {{ Form::hidden('reason', 'induction') }}
                {{ Form::hidden('source', 'manual') }}
                {{ Form::submit('Mark Paid', array('class'=>'btn btn-default btn-xs')) }}
                {{ Form::close() }}
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>