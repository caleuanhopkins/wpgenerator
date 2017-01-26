<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="large-text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

	<label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e('Select Article Type:');?></label>
	<select class="widefat" id="<?php echo $this->get_field_id( 'posttype' ); ?>" name="<?php echo $this->get_field_name( 'posttype' ); ?>">
		<option value="0" <?php echo (empty($selectedType)? 'selected="selected"': ''); ?>>All</option>
		<option value="standard" <?php echo (!empty($selectedType) && $selectedType === 'standard'? 'selected="selected"': ''); ?>>Standard</option>
		<?php foreach($post_formats[0] as $vals): ?>
			<option value="<?php echo esc_attr($vals); ?>" <?php echo (!empty($selectedType) && $selectedType === $vals? 'selected="selected"': ''); ?>><?php echo esc_attr($vals)?></option>
		<?php endforeach; ?>
	</select>

	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number to show:' ); ?></label> 
	<input class="large-text" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" />
</p>