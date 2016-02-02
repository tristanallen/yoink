@extends('app')
@section('content')
<div class="row">
	<div class="col-sm-12 ">	
    <!-- Small boxes (Stat box) -->
	<?php
			$number = 0;
	?>
     @foreach($markets as $key => $market)

		 @if($number % 2 == 0)
				<div class="row">
		 @endif
		<?php
			++$number;
		?>
	
		<div class="col-sm-6 ">		  
		    	<div class="panel">
		    		<div class="panel-heading row">

		    			<div class="col-sm-10">
		    				<h4>{{ $market['event']['name'] }} <small>({{  date( 'd-m-Y H:i:s' , strToTime($market['event']['date']) )}})</small></h4>
			    			<h5>{{$market['name']}} <small>(market id: {{$market['market_id']}})</small></h5>	
		    			</div>
		    			<div class="col-sm-2">
		    				<button class="btn small btn-block get-bets" data-market="{{ json_encode($market) }}">get bets</button>
		    			</div>
			    		
			    		
			    	</div>
			    	<div class="panel-body">
				    
				    	<table class="table table-striped table-bordered table-responsive">
				    		<tr>
				    			<th>select id</th>
				    			<th>name</th>
				    			<th>size</th>
				    			<th>price</th>
				   				<th>status</th>
				   				<th>date stored</th>
				    		</tr>	
				    		@forEach($market['runner'] as $k => $odds)
				    			@if(!empty($odds))
				    			<tr>
				    				<td>{{ $odds['id']}}</td>
				    				<td>{{ $odds['name']}}</td>

				    				<td>{{$odds['size']}}</td>
				    				<td>{{$odds['price']}}</td>
									<td>{{$odds['status']}}</td>
									<td>{{ date('d-m-Y h:i:s', strToTime($odds['created_at'])) }}</td>
				   				</tr>
				    		
				    			@endIf
				    		@endForEach
				    	</table>
			    	</div>
		    	</div>
		</div>

<!--
	    <div class="col-sm-6 ">		  
		    	<div class="panel">
		    		<div class="panel-heading">
		    			<h4>{{$market['name']}} <small><a href="/market/{{$market['id']}}">{{$market['market_id']}}</a></small></h4>
			    	</div>
			    	<div class="panel-body">
				    	latests odds
			    	</div>
		    	</div>
		</div>

 	-->

		@if($number % 2 == 0)
			</div>
		@endif


		@endForEach
	</div>
</div>	
@endsection