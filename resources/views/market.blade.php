@extends('app')
@section('content')
<div class="row">
	<div class="col-sm-12 ">

		<div class="panel">
			<div class="panel-body">

				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Line Chart</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="chart">
							<canvas id="lineChart" style="height:250px"></canvas>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
				<div class="row">
					<div class="col-sm-offset-10 col-sm-1"><strong style="color: rgba(166, 216, 255, 1)">Back</strong></div>
					<div class="col-sm-1"><strong style="color: rgba(246, 148, 170, 1)">Lay</strong></div>
				</div>

			</div>
		</div>
		<div class="panel">
			<div class="panel-body">

				<table class="table table-bordered table-responsive">
					<tr>
						<th>event name</th>
						<th>event date</th>
						<th>market id</th>
						<th>select id</th>
						<th>name</th>
						<th>status</th>
						<th>date stored</th>
						<th>odds</th>
					</tr>



						@foreach($market['runner'] as $k => $odds)
							@if(!empty($odds))
							<tr>
								<td>{{ $market['event']['name'] }}</td>
								<td>({{  date( 'd-m-Y H:i:s' , strToTime($market['event']['date']) )}})</td>
								<td>{{$market['bf_market_id']}}</td>
								<td>{{ $odds['id']}}</td>
								<td>{{ $odds['name']}}</td>
								<td>{{$odds['status']}}</td>
								<td>{{ date('d-m-Y H:i:s', strToTime($odds['created_at'])) }}</td>
								<td style="width: 40%">
									<div class="btn-group-justified">
										@foreach( array_reverse( $odds['backs'] ) as $back)
											<div class="btn" class="back" style="background: rgba(166, 216, 255, 0.7)">
												{{$back['price']}}<br><small>{{$back['size']}}</small>
											</div>
										@endforeach
										@foreach($odds['lays'] as $lay)
												<div class="btn" class="lay" style="background: rgba(246, 148, 170, 0.7)">
													{{$lay['price']}}<br><small>{{$lay['size']}}</small>
												</div>
										@endforeach


									</div>
								</td>
							</tr>



							@endIf
						@endforeach



				</table>
			</div>
		</div>

		<script src="{{ asset('plugins/chartjs/Chart.js') }}"></script>
		<script>
			var data = JSON.parse( '<?php echo $chartData ?>' );
			console.log(data);
			var ctx = document.getElementById("lineChart").getContext("2d");
			var myLineChart = new Chart(ctx).Line(data);
		</script>

	</div>
</div>	
@endsection