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
						<h5 style="display:inline">{$NEW_FIELD_FOR_X}</h5>
                        <div class="float-md-right">
							<a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                        </div>
						<hr>
						
                        {if isset($SUCCESS)}
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h5><i class="icon fa fa-check"></i> {$SUCCESS_TITLE}</h5>
                                {$SUCCESS}
                            </div>
                        {/if}

                        {if isset($ERRORS) && count($ERRORS)}
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
                                <ul>
                                    {foreach from=$ERRORS item=error}
                                        <li>{$error}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        {/if}
						
						<form role="form" action="" method="post">
							<div class="form-group">
								<label for="InputName">{$FIELD_NAME}</label>
								<input type="text" name="field_name" class="form-control" id="InputName" placeholder="{$FIELD_NAME}">
							</div>
							<div class="form-group">
								<label for="type">{$TYPE}</label>
								<select class="form-control" id="type" name="type">
									<option value="1">{$TYPES.1}</option>
									<option value="2">{$TYPES.2}</option>
									<option value="3">{$TYPES.3}</option>
								</select>
							</div>
							<div class="form-group">
								<label for="InputOptions">{$OPTIONS} - {$OPTIONS_HELP}</label>
								<textarea rows="5" class="form-control" name="options" id="options" placeholder="{$OPTIONS}"></textarea>
							</div>
                            <div class="form-group">
                                <label for="InputOrder">{$FIELD_ORDER}</label>
                                <input type="number" min="1" class="form-control" id="InputOrder" name="order" value="5">
                            </div>
							<div class="form-group">
								<label for="inputrequired">{$REQUIRED}</label>
								<input id="inputrequired" name="required" type="checkbox" class="js-switch" />
							</div>
							<div class="form-group">
								<input type="hidden" name="token" value="{$TOKEN}">
								<input type="submit" class="btn btn-primary" value="{$SUBMIT}">
							</div>
						</form>
						
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