<b>Hello</b>
@if($type == 0)
<p>Your Pin For Registration is {{ $info }}.</p>
@else
<p>Below is your registration link.</p>
<p>{{ url('api/register/'.$info) }}</p>
<p>copy above link & paste in postman and fill username & password to process</p>
@endif