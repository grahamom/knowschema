<?php
class KnowSchema_Deactivator {
	public static function deactivate() {
		// Deactivation logic here
		flush_rewrite_rules();
	}
}
