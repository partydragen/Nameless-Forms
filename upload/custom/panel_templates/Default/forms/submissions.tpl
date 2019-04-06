{include file='header.tpl'}
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    {include file='navbar.tpl'}
    {include file='sidebar.tpl'}
	
	<div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{$FORMS}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">{$FORMS}</li>
							<li class="breadcrumb-item active">{$SUBMISSIONS}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
		
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {if isset($NEW_UPDATE)}
                {if $NEW_UPDATE_URGENT eq true}
                <div class="alert alert-danger">
                    {else}
                    <div class="alert alert-primary alert-dismissible" id="updateAlert">
                        <button type="button" class="close" id="closeUpdate" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    {/if}
                    {$NEW_UPDATE}
                    <br />
                    <a href="{$UPDATE_LINK}" class="btn btn-primary" style="text-decoration:none">{$UPDATE}</a>
                    <hr />
                    {$CURRENT_VERSION}<br />
                    {$NEW_VERSION}
                </div>
                {/if}
				
                <div class="card">
                    <div class="card-body">
						<h3 style="display:inline;">{$SUBMISSIONS}</h3>
						
						<hr>
						
						{if count($SUBMISSIONS_LIST)}
						<div class="table-responsive">
                            <table class="table table-bordered">
								<thead>
                                    <tr>
									  <th>{$FORM}</th>
									  <th>{$USER}</th>
									  <th>{$UPDATED_BY}</th>
									  <th>{$STATUS}</th>
									  <th>{$ACTIONS}</th>
                                    </tr>
								</thead>
                                <tbody>
									{foreach from=$SUBMISSIONS_LIST item=submission}
                                    <tr>
										<td>{$submission.form_name}</td>
										<td>
										  {if !empty($submission.user_avatar)}
											<a href="{$submission.user_profile}" style="{$submission.user_style}"><img src="{$submission.user_avatar}" style="max-height:25px;max-width:25px;" alt="{$submission.user_name}" class="rounded"> {$submission.user_name}</a>
										  {else}
										    <i class="fa fa-user"></i> {$submission.user_name}
										  {/if}
										  <br /><span data-toggle="tooltip" data-original-title="{$submission.reported_at_full}">{$submission.created_at}</span>
										</td>
										<td>
										  {if !empty($submission.updated_by_avatar)}
											<a href="{$submission.updated_by_profile}" style="{$submission.updated_by_style}"><img src="{$submission.updated_by_avatar}" style="max-height:25px;max-width:25px;" alt="{$submission.updated_by_name}" class="rounded"> {$submission.updated_by_name}</a>
										  {else}
										    <i class="fa fa-user"></i> {$submission.updated_by_name}
										  {/if}
										  <br /><span data-toggle="tooltip" data-original-title="{$submission.reported_at_full}">{$submission.updated_at}</span>
										</td>
										<td><h5>{$submission.status}</h5></td>
										<td><a href="{$submission.link}" class="btn btn-primary">{$VIEW} &raquo;</a></td>
                                    </tr>
									{/foreach}
                                </tbody>
							</table>
						</div>
						{$PAGINATION}
                        {else}
                            {$NO_SUBMISSIONS}
                        {/if}
					</div>
				</div>
            </div>
        </section>
	</div>
	
    {include file='footer.tpl'}

</div>
<!-- ./wrapper -->

{include file='scripts.tpl'}

</body>
</html>