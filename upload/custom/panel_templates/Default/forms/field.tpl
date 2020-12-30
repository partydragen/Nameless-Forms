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
                        <h5 style="display:inline">{$EDITING_FIELD_FOR_X}</h5>
                        <div class="float-md-right">
							<a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                        </div>
						<hr>
						
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
						
						<form role="form" action="" method="post">
							<div class="form-group">
								<label for="InputName">{$FIELD_NAME}</label>
								<input type="text" name="field_name" class="form-control" id="InputName" placeholder="{$FIELD_NAME}" value="{$FIELD_NAME_VALUE}">
							</div>
							<div class="form-group">
								<label for="type">{$TYPE}</label>
								<select class="form-control" id="type" name="type">
									<option value="1"{if $TYPE_VALUE eq 1} selected{/if}>{$TYPES.1}</option>
									<option value="2"{if $TYPE_VALUE eq 2} selected{/if}>{$TYPES.2}</option>
									<option value="3"{if $TYPE_VALUE eq 3} selected{/if}>{$TYPES.3}</option>
									<option value="4"{if $TYPE_VALUE eq 4} selected{/if}>{$TYPES.4}</option>
									<option value="5"{if $TYPE_VALUE eq 5} selected{/if}>{$TYPES.5}</option>
								</select>
							</div>
							<div class="form-group">
								<label for="InputOptions">{$OPTIONS} - {$OPTIONS_HELP}</label>
								<textarea rows="5" class="form-control" name="options" id="options" placeholder="{$OPTIONS}">{$OPTIONS_VALUE}</textarea>
							</div>
                            <div class="form-group">
                                <label for="InputOrder">{$FIELD_ORDER}</label>
                                <input type="number" min="1" class="form-control" id="InputOrder" name="order" value="{$ORDER_VALUE}">
                            </div>
							<div class="form-group">
								<label for="inputrequired">{$REQUIRED}</label>
								<input id="inputrequired" name="required" type="checkbox" class="js-switch"{if $REQUIRED_VALUE eq 1} checked{/if} />
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