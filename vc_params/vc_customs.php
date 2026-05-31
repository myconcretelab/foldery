<?php 
	/** 
	* ZK Charixy will make some custom option for default VC Element 
	* @author Chinh Duong Manh
	* @since 1.0.0
	**/
	/* VC Row */
	add_action( 'vc_after_init', 'zk_charixy_add_vc_row_parallax_new_style' ); 
	function zk_charixy_add_vc_row_parallax_new_style() {
		$param = WPBMap::getParam( 'vc_row', 'parallax' );
        $param['value'][esc_html__( 'Simple 2', 'foldery' )]        = ' cms-bg-fixed';
        $param['value'][esc_html__( 'Overlay Background Color - Default', 'foldery' )]        = ' mask mask-default';
		vc_update_shortcode_param( 'vc_row', $param );
	}
?>