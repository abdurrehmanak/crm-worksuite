@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }} #{{ $lead->id }} - <span
                        class="font-bold">{{ ucwords($lead->company_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.leads.index') }}">{{ $pageTitle }}</a></li>
                <li class="active">@lang('modules.projects.files')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')

<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <nav>
                            <ul>
                                <li class="tab-current"><a href="{{ route('admin.leads.show', $lead->id) }}"><span>@lang('modules.lead.profile')</span></a>
                                </li>
                                <li><a href="{{ route('admin.proposals.show', $lead->id) }}"><span>@lang('modules.lead.proposal')</span></a></li>
                                <li ><a href="{{ route('admin.lead-files.show', $lead->id) }}"><span>@lang('modules.lead.file')</span></a></li>
                                <li><a href="{{ route('admin.leads.followup', $lead->id) }}"><span>@lang('modules.lead.followUp')</span></a></li>
                                @if($gdpr->enable_gdpr)
                                    <li><a href="{{ route('admin.leads.gdpr', $lead->id) }}"><span>GDPR</span></a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="files-list-panel">

                                        <div class="white-box">
                                            <div class="row">
                                                <div class="col-xs-6 b-r"> <strong>@lang('modules.lead.companyName')</strong> <br>
                                                    <p class="text-muted">{{ ucwords($lead->company_name) }}</p>
                                                </div>
                                                <div class="col-xs-6"> <strong>@lang('modules.lead.website')</strong> <br>
                                                    <p class="text-muted">{{ $lead->website ?? 'NA'}}</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-xs-6 b-r"> <strong>@lang('modules.lead.mobile')</strong> <br>
                                                    <p class="text-muted">{{ $lead->mobile ?? 'NA'}}</p>
                                                </div>
                                                <div class="col-xs-6"> <strong>@lang('modules.lead.address')</strong> <br>
                                                    <p class="text-muted">{{ $lead->address ?? 'NA'}}</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-xs-6 b-r" > <strong>@lang('modules.lead.clientName')</strong> <br>
                                                    <p class="text-muted">{{ $lead->client_name ?? 'NA'}}</p>
                                                </div>
                                                <div class="col-xs-6"> <strong>@lang('modules.lead.clientEmail')</strong> <br>
                                                    <p class="text-muted">{{ $lead->client_email ?? 'NA'}}</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                @if($lead->source_id != null)
                                                <div class="col-xs-6 b-r"> <strong>@lang('modules.lead.source')</strong> <br>
                                                    <p class="text-muted">{{ $lead->lead_source->type ?? 'NA'}}</p>
                                                </div>
                                                @endif
                                                @if($lead->status_id != null)
                                                <div class="col-xs-6"> <strong>@lang('modules.lead.status')</strong> <br>
                                                    <p class="text-muted">{{ $lead->lead_status->type ?? 'NA'}}</p>
                                                </div>
                                                @endif
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-xs-12"> <strong>@lang('app.note')</strong> <br>
                                                    <p class="text-muted">{{ $lead->note ?? 'NA'}}</p>
                                                </div>
                                            </div>
                                        </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

@endsection

@push('footer-script')

@endpush