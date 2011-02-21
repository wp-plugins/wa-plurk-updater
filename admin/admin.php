<?php
//SETS DEFAULT OPTIONS
	if(get_option('waplurkInitialised') != '1'){
		update_option('newpost-created-update', '1');
		update_option('newpost-created-text', 'Created a new post! #title#');
		update_option('newpost-created-say', 'has');
		
		update_option('newpost-edited-update', '1');
		update_option('newpost-edited-text', 'Editing a blog post..#title#');
		update_option('newpost-edited-say', 'is');

		update_option('newpost-published-update', '1');
		update_option('newpost-published-text', 'Published a new post: #title#');
		update_option('newpost-published-say', 'has');
		update_option('newpost-published-showlink', '1');

		update_option('oldpost-edited-update', '1');
		update_option('oldpost-edited-text', 'Fiddling with my blog post: #title#');
		update_option('oldpost-edited-say', 'is');
		update_option('oldpost-edited-showlink', '1');

		update_option('waplurkInitialised', '1');
	}
	if($_POST['submit-type'] == 'options'){
		//UPDATE OPTIONS
		update_option('newpost-created-update', $_POST['newpost-created-update']);
		update_option('newpost-created-text', $_POST['newpost-created-text']);
		update_option('newpost-created-say', $_POST['newpost-created-say']);
		
		update_option('newpost-edited-update', $_POST['newpost-edited-update']);
		update_option('newpost-edited-say', $_POST['newpost-edited-say']);

		update_option('newpost-published-update', $_POST['newpost-published-update']);
		update_option('newpost-published-text', $_POST['newpost-published-text']);
		update_option('newpost-published-say', $_POST['newpost-published-say']);
		update_option('newpost-published-showlink', $_POST['newpost-published-showlink']);

		update_option('oldpost-edited-update', $_POST['oldpost-edited-update']);
		update_option('oldpost-edited-text', $_POST['oldpost-edited-text']);
		update_option('oldpost-edited-say', $_POST['oldpost-edited-say']);
		update_option('oldpost-edited-showlink', $_POST['oldpost-edited-showlink']);

	}else if ($_POST['submit-type'] == 'login'){
		//UPDATE LOGIN
		if(($_POST['plurklogin'] != '') AND ($_POST['plurkpw'] != '')){
			$plurk = new plurk_api();
			$plurk->login(WAPLURK_API_KEY, $_POST['plurklogin'], $_POST['plurkpw']);
			$profile = $plurk->get_own_profile();
			$disName = $profile->user_info->display_name;
			if ($disName) {
				update_option('plurklogin', $_POST['plurklogin']);
				update_option('plurklogin_encrypted', base64_encode($_POST['plurklogin'].'[]'.$_POST['plurkpw']));
				$err = '<div class="updated">This is your display name in Plurk - "<em>' . $disName . '"</em> , correct? <strong>Congratulations!</strong> You may now proceed.</div>';
			} else {
				$err = '<div class="error">Username or Password is incorrect!</div>';
			}	
		}else{
			echo("<div style='border:1px solid red; padding:20px; margin:20px; color:red;'>You need to provide your twitter login and password!</div>");
		}
	} else if ($_POST['submit-type'] == 'remove-user'){
		delete_option('plurklogin');
		delete_option('plurklogin_encrypted');
	}
	// FUNCTION to see if checkboxes should be checked
	function df_checkCheckbox($theFieldname){
		if( get_option($theFieldname) == '1'){
			echo('checked="true"');
		}
	}

?>

<div class="wrap">
<div id="icon-users" class="icon32"><br></div>
	<h2>Your Plurk account details</h2>
	
	<form method="post" >
	<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="plurklogin">Your Plurk Username: <span class="description">(required)</span></label><input type="hidden" name="submit-type" value="login"></th>
		<td><input type="text" name="plurklogin" id="plurklogin" value="<?php echo(get_option('plurklogin')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="plurkpw">Your Plurk Password: <span class="description">(required)</span></label></th>
		<td><input type="password" name="plurkpw" id="plurkpw" value="" /> <?php if (get_option('plurklogin_encrypted')) { echo '<span class="description">(password already saved)</span>';
 }?></td>
	</tr>
	</table>
	<?php
	if ($err) {
	echo $err;
	}
	?>
	<input type="submit" name="submit" value="save login" class="button-primary" />
	</form>
</div>


<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
<h2>Plurk Updater</h2>

<form method="post">
	<div>
		<fieldset>
			<legend>New post created</legend>
			<p>
				<input type="checkbox" name="newpost-created-update" id="newpost-created-update" value="1" <?php df_checkCheckbox('newpost-created-update')?> />
				<label for="newpost-created-update">Update Plurk when a new post is created (saved but not published)</label>
			</p>
			<p>
				<label for="newpost-created-text">Text for this Plurk update ( use #title# as placeholder for page title )</label><br />
				<input type="text" name="newpost-created-say" id="newpost-created-say" size="10" maxlength="146" value="<?php echo(get_option('newpost-created-say')) ?>" />
				<input type="text" name="newpost-created-text" id="newpost-created-text" size="60" maxlength="146" value="<?php echo(get_option('newpost-created-text')) ?>" />
			</p>
		</fieldset>

		<fieldset>
			<legend>New post edited</legend>
			<p>
				<input type="checkbox" name="newpost-edited-update" id="newpost-edited-update" value="1" <?php df_checkCheckbox('newpost-edited-update')?> />
				<label for="newpost-edited-update">Update Plurk when the new post is edited (re-saved but not published)</label>
			</p>
			<p>
				<label for="newpost-edited-text">Text for this Plurk update ( use #title# as placeholder for page title )</label><br />
				<input type="text" name="newpost-edited-say" id="newpost-edited-say" size="10" maxlength="146" value="<?php echo(get_option('newpost-edited-say')) ?>" />
				<input type="text" name="newpost-edited-text" id="newpost-edited-text" size="60" maxlength="146" value="<?php echo(get_option('newpost-edited-text')) ?>" />
			</p>
		</fieldset>
		
		<fieldset>
			<legend>New post published</legend>
			<p>
				<input type="checkbox" name="newpost-published-update" id="newpost-published-update" value="1" <?php df_checkCheckbox('newpost-published-update')?> />
				<label for="newpost-published-update">Update Plurk when the new post is published</label>
			</p>
			<p>
				<label for="newpost-published-text">Text for this Plurk update ( use #title# as placeholder for page title )</label><br />
				<input type="text" name="newpost-published-say" id="newpost-published-say" size="10" maxlength="146" value="<?php echo(get_option('newpost-published-say')) ?>" />
				<input type="text" name="newpost-published-text" id="newpost-published-text" size="60" maxlength="146" value="<?php echo(get_option('newpost-published-text')) ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="newpost-published-showlink" id="newpost-published-showlink" value="1" <?php df_checkCheckbox('newpost-published-showlink')?> />
				<label for="newpost-published-showlink">Link title to blog?</label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>Existing posts</legend>
			<p>
				<input type="checkbox" name="oldpost-edited-update" id="oldpost-edited-update" value="1" <?php df_checkCheckbox('oldpost-edited-update')?> />
				<label for="oldpost-edited-update">Update Plurk when the an old post has been edited</label>
			</p>
			<p>
				<label for="oldpost-edited-text">Text for this Plurk update ( use #title# as placeholder for page title )</label><br />
				<input type="text" name="oldpost-edited-say" id="oldpost-edited-say" size="10" maxlength="146" value="<?php echo(get_option('oldpost-edited-say')) ?>" />
				<input type="text" name="oldpost-edited-text" id="oldpost-edited-text" size="60" maxlength="146" value="<?php echo(get_option('oldpost-edited-text')) ?>" />
				&nbsp;&nbsp;
				<input type="checkbox" name="oldpost-edited-showlink" id="oldpost-edited-showlink" value="1" <?php df_checkCheckbox('oldpost-edited-showlink')?> />
				<label for="oldpost-edited-showlink">Link title to blog?</label>
			</p>
		</fieldset>

		<input type="hidden" name="submit-type" value="options">
		<input type="submit" name="submit" value="save options" />
	</div>
	</form>

</div>

<div class="wrap">
<div id="icon-users" class="icon32"><br></div>
	<h2>Remove Plurk account details here</h2>

	<form method="post">
    	<input type="hidden" name="submit-type" value="remove-user">
    	<input type="submit" name="submit" value="Remove Me" />
    </form>

</div>