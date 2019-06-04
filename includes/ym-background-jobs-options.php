<?php
/*
Plugin Name: YM Background Jobs
Plugin URI: https://yakub.xyz/
Description: The plugin tests the implementation of background jobs
Author: M Yakub Mizan
Version: 1.0.0
Author URI: https://yakub.xyz
*/

class YM_Background_Jobs_Options {

	/**
	 * This is the place where we define our hooks
	 * They run when the class is instantiated. 
	**/
	public function __construct()
	{
		//hook the function that will call the add_options_page to add the options
		add_action( 'admin_menu', array($this, 'menu_callback') );
	}

	public function menu_callback()
	{
		add_options_page( 'YM Background Jobs', 'YM Background Jobs', 'manage_options', 'ym-bg-options', array($this, 'menu_markup') );
	}

	public function menu_markup()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
	?>
		<div class="wrap">
			<h1>YM Background Jobs</h1>
			<p> There are many different ways to do background processing in WordPress. This plugin demonstrates one of many ways used by WordPress Plugins. The plugin uses php's sleep() function with a random value between 1 and 5 to mimic a time-demanding operation. It gracefully handles failure, and allows the user to abort the operation anytime. </p>
			<table class="form-table">
		        <tr valign="top">
		        	<th scope="row">How many times to loop sleep()?</th>
		        	<td><input type="text" name="ymbgs_count" value="<?php echo rand(10, 5000); ?>" /></td>
		        </tr>
		    </table>
		    <p style='display:none;' id='ymbg-start-stop-progress'> Starting..... <br /> </p>
		    <p class="submit">
		    	<input type="submit" name="submit" id="submit" class="button button-primary ymbg-start-stop-btn" value="Start">
		    </p>
		</div>
	<?php
	}
}


new YM_Background_Jobs_Options();
