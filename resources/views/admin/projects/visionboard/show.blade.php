@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="border-bottom col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">Visionboard</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
.mx-0{margin-left: 0; margin-right: 0;}
.py-3 {padding: 40px 0;}
.main-img{margin-bottom: 25px; width: 100%; border: 1px solid #969494}
.details-section {margin-bottom: 35px;}
.details-section h4{ color: #000; font-weight: 600; font-size: 20px; line-height: 24px; text-transform: uppercase;}
.date{color: #000;}
.py-0{padding-left: 0; padding-right: 0;}
.client-text p{background: #000 !important; border: 1px solid #EEEEEE; padding: 10px 30px !important; color: #fff; font-size: 12px; font-weight: 400; font-family: 'Poppins'; border-radius: 15px 0 15px 0;}
.chat-text {margin: 5px 0;}
.chat-text p{margin: 0;}
.user-reply-section {margin-top: 15px; margin-bottom: 10px;}
.user-text p {background: #fff !important; border: 1px solid #EEEEEE; padding: 10px 20px !important; color: #5A5A5A; font-size: 12px; font-weight: 400; font-family: 'Poppins'; border-radius: 0 15px 0 15px;}
.type-msg input {border: 1px solid #EAEAEA; height: 51px;}
.send-btn input {background: #000 !important; padding: 10px; border-radius: 15px;}
.send-msg-section .row {display: flex; align-items: center; align-content: center;}
.send-msg-section {margin-bottom: 90px;}
.reset-btn button {background: #ED0404; color: #fff; border: 0; padding: 6px 17px;}
#visionboard .modal-title{text-align:left;}
.summary-area .full{display:block !important;width:100%;font-size:10px;text-align:right;}
.summary-area .full.hidden{visibility:hidden}
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    @include('admin.projects.show_project_menu')
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="issues-list-panel">
                                    <div class="white-box p-0">
                                        <h2 class="border-bottom p-b-10">Visionboard</h2>
										<div class="Inner-design py-3">
                          <div class="row">
                <div class="col-md-8">
                  <img class="main-img" data-toggle="toggle" data-target="#visionboard" src="{{ asset('img/lg-access.png') }}" alt="lg-accessories"></button>
                  <div class="row">
                    <div class="col-md-3">
                      <img src="{{ asset('img/sm-access.png') }}" alt="small-access">
                    </div><!--end of col-3-->
                    <div class="col-md-3">
                      <img src="{{ asset('img/sm-access.png') }}" alt="small-access">
                    </div><!--end of col-3-->
                    <div class="col-md-3">
                      <img src="{{ asset('img/sm-access.png') }}" alt="small-access">
                    </div><!--end of col-3-->
                    </div><!--end of inner-row-->

                </div><!--end of col-md-8-->
                <div class="col-md-4">
                  <div class="details-section">
                    <h4>Details</h4>
                    <div class="col-12">
                    <span><strong>Board Name:</strong> Slawson Living Room</span>
                    </div><!--end of col-12-->
                    <div class="col-12">
                    <span><strong>Board Unit Cost:</strong> $13,200</span>
                    </div><!--end of col-12-->
                    <div class="col-12">
                    <span><strong>Date Added To Project:</strong> 10/22/2022</span>
                    </div><!--end of col-12-->
                    <div class="col-12">
                    <span><strong>Client Status:</strong> APPROVED</span>
                    </div><!--end of col-12-->
                  </div><!--end of details section-->
                  <div class="chat-section">
                    <div class="client-section-chat">
                    <div class="row mx-0">
                      <div class="col-md-3">       </div><!--end of col-md-3-->
                      <div class="col-md-7 date text-right">
                        10/22/2022
                      </div><!--end of col-6 date-->
                    </div><!--end of row-->

                    <div class="row mx-0">
                      <div class="col-md-3">       </div><!--end of col-md-3-->
                      <div class="col-md-7 chat-text client-text py-0">
                        <p>I’m fine, i’m thinking about new project. I want to open an online store</p>
                      </div><!--end of col-6 date-->
                      <div class="col-md-2">
                        <img src="{{ asset('img/chat-img.jpg') }}" alt="">
                      </div>
                    </div><!--end of row-->

                    <div class="row mx-0">
                      <div class="col-md-3">       </div><!--end of col-md-3-->
                      <div class="col-md-7 chat-text client-text py-0">
                        <p>But I don’t know what to sell. Maybe I will sell stones and water</p>
                      </div><!--end of col-6 date-->
                      <div class="col-md-2">
                      </div>
                    </div><!--end of row-->
                    </div><!--end of client-section-chat-->

                    <div class="user-reply-section">

                      <div class="row mx-0">
                        <div class="col-md-2">       </div><!--end of col-md-1-->
                        <div class="col-md-7 date text-left py-0">
                          10/22/2022
                        </div><!--end of col-6 date-->
                        <div class="col-md-4">       </div><!--end of col-md-4-->
                      </div><!--end of row-->

                      <div class="row mx-0">
                        <div class="col-md-2">
                          <img src="{{ asset('img/chat-img.jpg') }}" alt="">
                          </div><!--end of col-md-3-->
                        <div class="col-md-7 chat-text user-text py-0">
                          <p>Yeah it’s great idea, you know - everyone needs water, I dont know about stones xD</p>
                        </div><!--end of col-6 date-->
                        <div class="col-md-2">
                        </div>
                      </div><!--end of row-->

                    </div><!--end of user-reply-section-->

                    <div class="send-msg-section">
                      <div class="row mx-0">
                        <div class="col-md-10 type-msg">
                            <input type="text" class="form-control" placeholder="Say Something">
                        </div><!--end of col-10-->
                        <div class="col-md-2 send-btn">
                           <input type="image" src="{{ asset('img/send-icon.png') }}" alt="Submit">
                        </div><!--end of col-2-->
                      </div><!--end of row-->
                    </div><!--end of send-msg-section-->

                      <div class="col-md-12 reset-btn text-right">
                      <button type="button" name="button">Reset Status</button>
                      </div><!--end of col-md-12 reset-btn-->

                  </div><!--end of chat-section-->
                </div><!--end of col-md-4-->
              </div><!--end of row-->

    </div><!--end of inner-design-->
                                    </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
<div class="modal fade" id="visionboard" tabindex="-1" role="dialog" aria-labelledby="visionboardLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h4 class="modal-title pull-left" id="visionboardLabel">Make your selections</h4>
		<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
      </div>
      <div class="modal-body">
        <div class="product-con-area">
			<div class="row">
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/93/1613496938.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="fa fa-times-circle"></i>
								<i class="disable fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full hidden">Cost:$429</span>
						</div>
					</div>
				</div>
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/94/1613503192.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="disable fa fa-times-circle"></i>
								<i class=" fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full">Cost:$429</span>
						</div>
					</div>
				</div>
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/110/1614120572.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="fa fa-times-circle"></i>
								<i class="disable fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full hidden">Cost:$429</span>
						</div>
					</div>
				</div>
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/91/1613411252.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="fa fa-times-circle"></i>
								<i class="disable fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full hidden">Cost:$429</span>
						</div>
					</div>
				</div>
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/120/1614980868.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="disable fa fa-times-circle"></i>
								<i class="fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full">Cost:$429</span>
						</div>
					</div>
				</div>
				<div class="col-md-4 location-code-cls uncategorized-cls declined-cls">
					<div class="category-box">
						<div>
							<a href="javascript:void(0)"><img id="111" data-product-id="111" src="https://stagin.indema.co/user-uploads/products/2667/163415577072512929.jpg" class="img-responsive p-image" alt="Indema Product 1"></a>
						</div>
						<div class="summary-area">
							<span>
								<input name="product_id" value="111" type="checkbox">
								<a style="display: none;" href="javascript:void(0)" class="lock-clicked" id="lock-clicked-111" data-pp-id="111"><i id="p-lock-111" class="fa fa-lock"></i></a>
							</span>
							<span>
								<i class="fa fa-times-circle"></i>
								<i class="disable fa fa-check-circle"></i>
								<a href="javascript:void(0)" class="view-product-notes" data-npp-id="111"><img src="https://stagin.indema.co/img/commets-icon.png" alt=""> </a> 0
							</span>
							<span class="full hidden">Cost:$429</span>
						</div>
					</div>
				</div>
			</div>
		</div>
      </div>
      <div class="modal-footer border-0">        
        <button type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('footer-script')
<script src="https://stagin.indema.co/plugins/bower_components/jquery/dist/jquery.min.js"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://stagin.indema.co/bootstrap/dist/js/bootstrap.min.js"></script>

<script>
	$(document).ready(function(){
		$(".main-img").click(function(){
			$('#visionboard').modal('show');
		});
	});
    $('ul.showProjectTabs .projectVisionboards').addClass('tab-current');
</script>
@endpush
