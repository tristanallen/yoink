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
						<th>status</th>
						<th>date stored</th>
						<th>odds</th>
					</tr>

            </div><!-- /.col (LEFT) -->
            <div class="col-md-6">
              <!-- LINE CHART -->
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
								<td>
									<div class="row">
										@foreach($odds['lays'] as $lay)
												<div class="col-sm-1">
													<strong>lay</strong><br>
													{{$lay['price']}}<br><small>{{$lay['size']}}</small>
												</div>
										@endforeach

										@foreach($odds['backs'] as $back)
											<div class="col-sm-1">
												<strong>back</strong><br>
												{{$back['price']}}<br><small>{{$back['size']}}</small>
											</div>
										@endforeach
									</div>
								</td>
							</tr>



							@endIf
						@endforeach
						<tr class="active"><td colspan="7" ></td></tr>



				</table>
			</div>
		</div>


	</div>
</div>	
@endsection