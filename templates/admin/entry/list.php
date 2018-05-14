<?php

use DialogContactForm\Entries\Entry_List_Table;

?>
<div class="wrap">

    <h1 class="wp-heading-inline">
		<?php echo __( 'Entries', 'dialog-contact-from' ); ?>
    </h1>
    <hr class="wp-header-end">

    <!-- Show error message if any -->
	<?php if ( array_key_exists( 'error', $_GET ) ): ?>
        <div class="notice notice-error is-dismissible"><p><?php echo $_GET['error']; ?></p></div>
	<?php endif; ?>

    <!-- Show success message if any -->
	<?php if ( array_key_exists( 'success', $_GET ) ): ?>
        <div class="notice notice-success is-dismissible"><p><?php echo $_GET['success']; ?></p></div>
	<?php endif; ?>

    <form id="movies-filter" method="get" autocomplete="off" accept-charset="utf-8">
        <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type'] ?>"/>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <input type="hidden" id="_dcf_nonce" name="_dcf_nonce"
               value="<?php echo wp_create_nonce( 'dcf_entries_list' ); ?>"/>
		<?php

		//Create an instance of our package class...
		$table = new Entry_List_Table();

		//Fetch, prepare, sort, and filter our data...
		$table->prepare_items();

		// Show search form
		$table->search_box( __( 'Search Entry', 'dialog-contact-from' ), 'entry' );

		// Display table with data
		$table->display();
		?>
    </form>

</div>
