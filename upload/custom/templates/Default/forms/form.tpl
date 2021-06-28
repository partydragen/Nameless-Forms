{include file='header.tpl'}
{include file='navbar.tpl'}
<div class="container">
  <div class="row">
    {if count($WIDGETS_LEFT)}
	  <div class="col-md-3">
		{foreach from=$WIDGETS_LEFT item=widget}
		  {$widget}
		  <br />
		{/foreach}
	  </div>
	{/if}
    
	<div class="col-md-{if count($WIDGETS_LEFT) && count($WIDGETS_RIGHT)}6{elseif count($WIDGETS_RIGHT) || count($WIDGETS_LEFT)}9{else}12{/if}">
      <div class="card">
        <div class="card-body">

			<h2>{$TITLE}</h2>
			<hr>
			
			{if isset($SUCCESS)}
			<div class="alert alert-success">
			    {$SUCCESS}
			</div>
			{/if}
			
			{if isset($ERRORS)}
			  <div class="alert alert-danger">
			  {foreach from=$ERRORS item=error}
			    {$error}<br />
			  {/foreach}
			  </div>
			{/if}
            
            {if isset($CONTENT)}
              {$CONTENT}
              </br></br>
            {/if}
			
			<form action="" method="post" id="forms">
			  {foreach from=$FIELDS item=field}
			  <div class="form-group">
				{if $field.type == 5}
				  <hr />
				{elseif $field.type == 4}
					{', '|implode:$field.options}
				{else}
				  <label for="{$field.id}">{$field.name} {if $field.required} <span class="text-danger"><strong>*</strong></span>{/if}</label>
				{/if}
				{if $field.type == "1"}
				<input type="text" class="form-control" name="{$field.id}" id="{$field.id}" value="{$field.value}" placeholder="{$field.name}">
				{elseif $field.type == "2"}
				<select name="{$field.id}" id="{$field.id}" class="form-control">
					{foreach from=$field.options item=option}
					<option value="{$option}" {if $option eq $field.value} selected{/if}>{$option}</option>
					{/foreach}
				</select>
				{elseif $field.type == "3"}
				<textarea class="form-control" name="{$field.id}" id="{$field.id}">{$field.value}</textarea>
                {elseif $field.type == "6"}
                <input type="number" class="form-control" name="{$field.id}" id="{$field.id}" value="{$field.value}" placeholder="{$field.name}">
                {elseif $field.type == "7"}
                <input type="email" class="form-control" name="{$field.id}" id="{$field.id}" value="{$field.value}" placeholder="{$field.name}">
				{/if}
			  </div>
			  {/foreach}
              
              {if $CAPTCHA}
                <div class="form-group">
                  {$CAPTCHA}
                </div>
              {/if}
              
			  <input type="hidden" name="token" value="{$TOKEN}">
			  <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
			</form>
        </div>
      </div>
    </div>

    {if count($WIDGETS_RIGHT)}
      <div class="col-md-3">
		{foreach from=$WIDGETS_RIGHT item=widget}
		  {$widget}
		  <br />
		{/foreach}
      </div>
	{/if}

  </div>
</div>
{include file='footer.tpl'}