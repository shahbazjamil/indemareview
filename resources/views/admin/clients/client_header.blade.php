<div class="col-md-12">
    <div class="white-box p-0">

        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body border-radius p-0">
                            <div class="row">
                                
                                <div class="col-md-12 border-bottom p-b-10 m-b-20">
                                    <div class="row">
                                        <div class="col-md-6 flex-row align-items-center">
										<img src="{{ $client->image_url }}" width="75" height="75" class="img-circle m-r-15" alt="">
                                            <p class="m-0">
                                                <span class="font-medium text-info- font-semi-bold">{{ ucwords($client->name) }}</span>
                                                <br>

                                                @if (!empty($client->client_details) && $client->client_details->company_name != '')
                                                   <span class="text-muted">{{ $client->client_details->company_name }}</span>  
                                                @endif
                                            </p>
                                            
                                            {{-- <p class="font-12">
                                                @lang('app.lastLogin'): 

                                                @if (!is_null($client->last_login)) 
                                                {{ $client->last_login->timezone($global->timezone)->format($global->date_format.' '.$global->time_format) }}
                                                @else
                                                --
                                                @endif
                                            </p> --}}
            
                                        </div>
<!--										<div class="col-md-6 text-right">
											<h3>Client Credit Balance: $350.00</h3>
											 <a href="#" class="btn btn-outline btn-success btn-sm">Send Retainer Request</a>
										</div>-->
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row project-top-stats front-dashboard">
                                        <div class="col-md-3">
											<h4 class="white-box">
												<span class="text-primary">
													{{ $clientStats->totalProjects}}
												</span> <span class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalProjects')</span>
											</h4>
                                        </div>        
                                        <div class="col-md-3">
											<h4 class="white-box">
												<span class="text-danger">
													{{ $clientStats->totalUnpaidInvoices}}
												</span> <span class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalUnpaidInvoices')</span>
											</h4>
                                        </div>
										<div class="col-md-3">
											<h4 class="white-box">
												<span class="text-success">
													{{ $clientStats->projectPayments + $clientStats->invoicePayments }}
												</span> <span class="font-12 text-muted m-l-5"> @lang('app.earnings')</span>
											</h4>
                                        </div>        
                                        <div class="col-md-3">
											<h4 class="white-box">
												<span class="text-primary">
													{{ $clientStats->totalContracts}}
												</span> <span class="font-12 text-muted m-l-5"> @lang('modules.contracts.totalContracts')</span>
											</h4>	
                                        </div>
                                    </div>
                                    
                                    <div class="row project-top-stats">
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>