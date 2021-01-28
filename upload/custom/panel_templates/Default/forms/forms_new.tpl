{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$FORMS}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$FORMS}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 style="display:inline">{$CREATING_NEW_FORM}</h5>
                        <div class="float-md-right">
							<a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                        </div>
						<hr>
						
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
						
						<form role="form" action="" method="post">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="InputName">{$FORM_NAME}</label>
										<input type="text" name="form_name" class="form-control" id="InputName" placeholder="{$FORM_NAME}">
									</div>
									<div class="form-group">
										<label for="InputUrl">{$FORM_URL}</label>
										<input type="text" name="form_url" class="form-control" id="InputURL" placeholder="{$FORM_URL}">
									</div>
								    <div class="form-group">
									  <label for="Inputguest">{$ALLOW_GUESTS}</label> <span class="badge badge-info"><i class="fas fa-question-circle" data-container="body" data-toggle="popover" data-placement="top" title="{$INFO}" data-content="{$ALLOW_GUESTS_HELP}"></i></span>
									  <input id="inputguest" name="guest" type="checkbox" class="js-switch" />
								    </div>
									<div class="form-group">
										<label for="groups_view">{$GROUPS_VIEW}</label>
										<select name="groups_view[]" id="groups_view" size="5" class="form-control" multiple style="overflow:auto;">
										{if count($ALL_GROUPS)}
											{foreach from=$ALL_GROUPS item=item}
											<option value="{$item.id}"{if $item.selected} selected{/if}>{$item.name}</option>
											{/foreach}
										{/if}
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="InputUrl">{$FORM_ICON}</label>
										<input type="text" name="form_icon" class="form-control" id="InputIcon" placeholder="{$FORM_ICON}">
									</div>
									<div class="form-group">
										<label for="link_location">{$FORM_LINK_LOCATION}</label>
											<select class="form-control" id="link_location" name="link_location">
											<option value="1">{$LINK_NAVBAR}</option>
											<option value="2">{$LINK_MORE}</option>
											<option value="3">{$LINK_FOOTER}</option>
											<option value="4">{$LINK_NONE}</option>
										</select>
									</div>
								    <div class="form-group">
									  <label for="Inputcan_view">{$CAN_USER_VIEW}</label> <span class="badge badge-info"><i class="fas fa-question-circle" data-container="body" data-toggle="popover" data-placement="top" title="{$INFO}" data-content="{$CAN_USER_VIEW_HELP}"></i></span>
									  <input id="inputcan_view" name="can_view" type="checkbox" class="js-switch" />
								    </div>
								</div>
							  </div>
							  <div class="form-group">
								<input type="hidden" name="token" value="{$TOKEN}">
								<input type="submit" class="btn btn-primary" value="{$SUBMIT}">
							  </div>
						</form>
                        
                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

</body>
</html>