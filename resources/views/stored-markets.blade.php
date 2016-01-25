@extends('app')
@section('content')
<div class="row">
	<div class="col-sm-12 ">	
    <!-- Small boxes (Stat box) -->
    <div class="row">
     @foreach($markets as $key => $market)
	
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

 	
	
	@endForEach
	</div>
	</div>
</div>	
@endsection