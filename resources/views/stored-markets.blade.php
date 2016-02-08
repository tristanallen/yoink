@extends('app')
@section('content')
<div class="row">
	<div class="col-sm-12 ">

		<div class="panel">
			<div class="panel-body">

				<table class="table table-bordered table-responsive">
					<tr>
						<th>event name</th>
						<th>event date</th>
						<th>market id</th>
					</tr>

					@foreach(array_reverse($markets)  as $key => $market)
						<tr>
							<td><a href="/saved-markets/{{$market['id']}}"> {{ $market['event']['name'] }} </a></td>
							<td>{{  date( 'd-m-Y H:i:s' , strToTime($market['event']['date']) )}}</td>
							<td>{{$market['bf_market_id']}}</td>
						</tr>

					@endForEach


				</table>
			</div>
		</div>


	</div>
</div>	
@endsection