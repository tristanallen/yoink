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
						<th>select id</th>
						<th>name</th>
						<th>size</th>
						<th>price</th>
						<th>status</th>
						<th>date stored</th>
					</tr>
					@foreach($markets as $key => $market)
						@forEach($market['runner'] as $k => $odds)
							@if(!empty($odds))
							<tr>
								<td>{{ $market['event']['name'] }}</td>
								<td>({{  date( 'd-m-Y H:i:s' , strToTime($market['event']['date']) )}})</td>
								<td>{{$market['market_id']}}</td>
								<td>{{ $odds['id']}}</td>
								<td>{{ $odds['name']}}</td>

								<td>{{$odds['size']}}</td>
								<td>{{$odds['price']}}</td>
								<td>{{$odds['status']}}</td>
								<td>{{ date('d-m-Y h:i:s', strToTime($odds['created_at'])) }}</td>
							</tr>


							@endIf
						@endforeach
						<tr class="active"><td colspan="9" ></td></tr>
					@endForEach

				</table>
			</div>
		</div>


	</div>
</div>	
@endsection