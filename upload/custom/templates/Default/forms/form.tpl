{include file='navbar.tpl'}
<div class="container">
  <div class="card">
	<div class="card-body">
	  {if !empty($WIDGETS)}
	  <div class="row">
		<div class="col-md-9">
	  {/if}
			<h2>{$TITLE}</h2>
			<hr>
			
			<form action="" method="post">
			  {foreach from=$FIELDS item=field}
			  <div class="form-group">
				<label for="input{$field.id}">{$field.name}</label>
				{if $field.type == "1"}
				<input type="text" class="form-control" name="{$field.id}" id="input{$field.id}" placeholder="{$field.name}">
				{elseif $field.type == "2"}
				<select name="type" id="type" class="form-control">
					{foreach from=$field.options item=option}
					<option value="{$option}">{$option}</option>
					{/foreach}
				</select>
				{elseif $field.type == "3"}
				<textarea class="form-control" name="{$field.id}" id="input{$field.id}"></textarea>
				{/if}
			  </div>
			  {/foreach}
			  <input type="hidden" name="token" value="{$TOKEN}">
			  <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
			</form>
	  {if !empty($WIDGETS)}
		</div>
		<div class="col-md-3">
		{foreach from=$WIDGETS item=widget}
		  {$widget}<br /><br />
		{/foreach}
		</div>
	  </div>
	  {/if}
	
	</div>
  </div>
</div>
{include file='footer.tpl'}