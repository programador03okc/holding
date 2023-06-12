<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
        <div class="alert alert-{{$msg}}">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            @switch($msg)
                @case('success')
                    <span class="glyphicon glyphicon-ok"></span> 
                @break
                @case('danger')
                    <span class="glyphicon glyphicon-remove"></span> 
                @break
                @case('warning')
                    <span class="glyphicon glyphicon-warning-sign"></span> 
                @break
            @endswitch
            {!!Session::get('alert-' . $msg)!!}
        </div>
        @endif
    @endforeach
</div>
