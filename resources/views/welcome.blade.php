@extends('app')
@section('content')
<div class="row">
	<div class="col-lg-12 ">	
    <!-- Small boxes (Stat box) -->
     @foreach($output['next_market'] as $key => $next_market)
     @if($key%2)
     	<div class="row">
 	 @endIf		
	    <div class="col-lg-6 ">		  
		    	<div class="panel">
		    		<div class="panel-heading row">

		    			<div class="col-lg-10">
		    				<h4>{{ $next_market['event']->name }} <small>({{  date( 'd-m-Y H:i:s' , strToTime($next_market['event']->openDate) )}})</small></h4>
			    			<h5>{{$next_market['marketName']}} <small>(market id: {{$next_market['marketId']}})</small></h5>	
		    			</div>
		    			<div class="col-lg-2">
		    				<button class="btn small btn-block tracker" >track</button>
		    			</div>
			    		
			    		
			    	</div>
			    	<div class="panel-body">
				    
				    	<table class="table table-striped table-bordered">
				    		<tr>
				    			<th>select id</th>
				    			<th>name</th>
				    			<th>size</th>
				    			<th>price</th>
				    			<th>status</th>
				    		</tr>	
				    		@forEach($next_market['bets'] as $k => $odds)
				    			@if(!empty($odds))
				    			<tr>
				    				<td>{{ $odds['id']}}</td>
				    				<td>{{ $odds['name']}}</td>

				    				<td>
				    					@if(!empty($odds['availableToLay']))
				    						{{ $odds['availableToLay']->size }}
				    					@endIf
				    				</td>
				    				<td>
				    					@if(!empty($odds['availableToLay']))
				    						{{ $odds['availableToLay']->price }}
				    					@endIf
				    				</td>
				    				<td>{{$odds['status']}}</td>
				   				</tr>
				    		
				    			@endIf
				    		@endForEach
				    	</table>
			    	</div>
		    	</div>
		</div>
	 @if($key%2)
     	</div>
 	 @endIf
	
	@endForEach
	</div>
</div>	
@endsection