<?php
class KnowSchema_Activator {
	public static function activate() {
		// Activation logic here (e.g., set default options, flush rewrite rules)
		// For MVP: Flush rewrite rules to ensure CPT works immediately
		flush_rewrite_rules();
	}
}
