@extends('app')
@section('content')
    <!-- Small boxes (Stat box) -->
     <div class="row">
	    <div class="col-lg-3 col-xs-6">
		    <h1>next market</h1>
		    @foreach($output['next_market'] as $next_market)
		    <h4>{{ $next_market->event[0]->event->name}} <small>{{$next_market->event[0]->event->openDate}}</small></h4>
		    <h5>{{$next_market->marketName }}</h5>
		    <ul>
			    <li><p><strong>market id:</strong> {{$next_market->marketId }}</p></li>
			    <li><p><strong>total matched:</strong> {{ $next_market->totalMatched }}</p></li>
				<!--<li><p><strong>Book</strong> {{ var_dump( $next_market->book )  }}</p></li>-->
				<li><strong>market books: </strong>
					<ul>
					 @foreach ($next_market->book[0] as $key => $value) 
				       		 <li>
				        		<strong>{{ $key }}:</strong> 
				      
					        	@if(is_array($value))
					        		<ul>
					        			@forEach($value as $k => $v)
						        			@forEach($v as $i => $runner)
						        				<li><strong>{{$i}}:</strong>
						        				@if(is_object($runner))

						        					@forEach($runner as $r => $run)
						        			
						        						<li><strong>{{$r}}:</strong>
						        							<ul>
						        								@forEach($run as $j => $ru)
						        									<li><strong>price {{$j}}: </strong>{{$ru->price}}</li>
						        									<li><strong>size {{$j}}: </strong>{{$ru->size}}</li>
						        								@endForEach

						        							</ul>
						        				
						        						</li>
						        					@endForEach
						        				@else
						        					 {{ $runner }}
						        				@endIf
							        			</li>
							        		@endForEach
							        	@endForEach
					        		</ul>
					        		
					        	@else
					        		{{$value}}

					        	@endIf
			        	  	</li>		
				        
				    @endforeach
				</ul>	
				</li>
				
				
			</ul>	
		    @endforeach
		    <div class="row">
		    	<h1>event types</h1>
		    	<p>{{var_dump( $output['event_types'])}}</p>
		    	<h1>football id</h1>
		    	<p>{{var_dump( $output['football_id'])}}</p>
		    </div>
		</div>
	</div>
@endsection