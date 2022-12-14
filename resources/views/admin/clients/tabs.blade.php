<div class="white-box p-0">
    <nav>
        <ul class="showClientTabs">
            <li class="clientProfile"><a href="{{ route('admin.clients.show', $client->id) }}" class="btn-default"><i class="icon-user"></i> <span>@lang('modules.employees.profile')</span></a>
            </li>
            <li class="clientProjects"><a href="{{ route('admin.clients.projects', $client->id) }}" class="btn-default"><i class="icon-layers"></i> <span>@lang('app.menu.projects')</span></a>
            </li>
            <li class="clientInvoices"><a href="{{ route('admin.clients.invoices', $client->id) }}" class="btn-default"><i class="icon-doc"></i> <span>@lang('app.menu.invoices')</span></a>
            </li>
            <li class="clientContacts"><a href="{{ route('admin.contacts.show', $client->id) }}" class="btn-default"><i class="icon-people"></i> <span>@lang('app.menu.contacts')</span></a>
            </li>
            <li class="clientPayments"><a href="{{ route('admin.clients.payments', $client->id) }}" class="btn-default"><i class="ti-receipt"></i> <span>@lang('app.menu.payments')</span></a>
            </li>
            <li class="clientNotes"><a href="{{ route('admin.clients.notes', $client->id) }}" class="btn-default"><i class="ti-notepad"></i> <span>@lang('app.notes')</span></a>
            </li>
            @if($gdpr->enable_gdpr)
            <li class="clientGdpr"><a href="{{ route('admin.clients.gdpr', $client->id) }}" class="btn-default"><i class="icon-lock"></i> <span>@lang('modules.gdpr.gdpr')</span></a>
            </li>
            @endif
            <li class="clientAudit"><a href="{{ route('admin.clients.audit', $client->id) }}" class="btn-default"><i class="fa fa-history"></i> <span>@lang('modules.audit.audit')</span></a>
            </li>
        </ul>
    </nav>
</div>