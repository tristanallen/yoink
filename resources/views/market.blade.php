@extends('app')
@section('content')
<div class="row">
	<div class="col-sm-12 ">	
    
	    <div class="row">
	    	 <div class="col-sm-6 ">		  
		    	<div class="panel">
		    		<div class="panel-heading row">

		    			<div class="col-sm-10">
		    				<h4>{{$market['name']}} <small>{{$market['market_id']}}</small></h4>
		    			</div>
		    			<div class="col-sm-2">
		    				<button class="btn small btn-block get-bets" data-market="{{ json_encode($market) }}">get bets</button>
		    			</div>
		    			
			    	</div>
			    	<div class="panel-body">
				    	latests odds
			    	</div>
		    	</div>
			</div>
		</div>
	</div>
</div>	
@endsection