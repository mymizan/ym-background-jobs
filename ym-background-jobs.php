<?php
/*
Plugin Name: YM Background Jobs
Plugin URI: https://yakub.xyz/
Description: The plugin tests the implementation of background jobs
Author: M Yakub Mizan
Version: 1.0.0
Author URI: https://yakub.xyz
*/

class YM_Background_Jobs {

	public function __construct()
	{
		if ( is_admin() )
		{
			add_action( 'admin_enqueue_scripts',      array($this, 'enqueue_assets') );
			add_action( 'wp_ajax_ymbgs_start_jobs',   array($this, 'start_jobs') );
			add_action( 'wp_ajax_ymbgs_process_jobs', array($this, 'process_jobs') );
			add_action( 'wp_ajax_ymbgs_stop_jobs',    array($this, 'stop_jobs') );
		}

		//include files
		require_once dirname( __FILE__ ) . '/includes/ym-background-jobs-options.php';
	}

	public function enqueue_assets()
	{
		wp_register_style( 'wmbjs-css', plugins_url( '/assets/css/main.css' , __FILE__), array() );
		wp_enqueue_style( 'wmbjs-css' );

		wp_register_script( 'wmbjs-js', plugins_url( '/assets/js/main.js' , __FILE__), array() );
		wp_enqueue_script( 'wmbjs-js' );
	}

	public function start_jobs() {
		$job_id = uniqid();
		$file_path = dirname(__FILE__) . '/storage/' . $job_id . ".json";
		$json_data = json_encode([
			'job_id' 	   => $job_id,
			'job_status'   => 'progress',
			'steps'		   => (int) $_POST['steps'],
			'current_step' => 0,
			'start_time'   => time(),
		]);

		file_put_contents( $file_path, $json_data);

		if ( file_exists($file_path) )
		{
			echo $json_data;
		} else {
			echo json_encode([
				'status' => 'failed',
				'message' => "Can not create job file", 
			]);
		}

		die;
	}

	public function process_jobs()
	{
		$job_id = $_POST['job_id'];
		$file_path = dirname(__FILE__) . '/storage/' . $job_id . ".json";
		$json = json_decode(file_get_contents($file_path));


		if ( ! file_exists($file_path) ) {
			echo json_encode([
				'status'  => 'failed',
				'message' => "Job does not exist.",
			]);
			die;
		}

		sleep( rand(1, 5) );

		$json = json_decode(file_get_contents($file_path)); //read the file again to see if we have a stop request
		$json->current_step += 1;

		if ($json->job_status == 'stop' || $json->current_step >= $json->steps )
		{
			//job finished
			unlink($file_path);
			echo json_encode([
				'status'  => 'completed',
				'time'    => (time() - $json->start_time)/60,
				'message' => "Job finished succesfully",
			]);
			die;
		}

		

		file_put_contents( $file_path, json_encode($json));
		echo json_encode($json);
		die;
	}

	public function stop_jobs()
	{
		$job_id = $_POST['job_id'];
		$file_path = dirname(__FILE__) . '/storage/' . $job_id . ".json";

		$json = json_decode(file_get_contents($file_path));
		$json->job_status = 'stop';
		file_put_contents( $file_path,json_encode($json));
		echo json_encode([
			'status'  => 'success',
			'message' => 'Stop Requested',
		]);
		die;
	}
}


new YM_Background_Jobs();
