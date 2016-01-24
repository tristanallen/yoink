@extends('app')
@section('content')
    <!-- Small boxes (Stat box) -->
     <div class="row">
	    <div class="col-lg-3 col-xs-6">
		    <h1>next market</h1>
		    @foreach($output['next_market'] as $next_market)
			   	<h4>{{ $next_market->event->name}} <small>{{ date('d-m-Y h:i:s', strToTime($next_market->event->openDate)) }}</small></h4>
			    <h5>{{$next_market->marketName }}</h5>
			    <strong>Teams:</strong>
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
						       
						        				<li><strong>runner name : {{$next_market->runners[$k]->runnerName}}</strong></li>
							        			@forEach($v as $i => $runner)

							        				<li><strong>{{$i}}:</strong>
							        				@if(is_object($runner))
							        					<ul>
							        					@forEach($runner as $r => $run)
							        		
							        						<li><strong>{{$r}}:</strong>
							        							<ul>
							        								@forEach($run as $j => $ru)
							        									<li>{{$ru->size}} @ {{$ru->price}}</li>
							        								@endForEach

							        							</ul>
							        				
							        						</li>
							        					@endForEach
							        				</ul>
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
		 
		</div>
	</div>
@endsection