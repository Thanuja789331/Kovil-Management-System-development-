<?php

require_once __DIR__ . "/Controller.php";

class LanguageController extends Controller {
    /**
     * Handle language switching
     */
    public function switch() {
        $lang = $_GET['lang'] ?? 'en';
        if (function_exists('setLanguage')) {
            setLanguage($lang);
        } else {
            // Fallback if helpers not loaded
            $supportedLanguages = ['en', 'ta'];
            if (in_array($lang, $supportedLanguages)) {
                $_SESSION['language'] = $lang;
            }
        }

        // Redirect back to the referring page or dashboard
        $referer = $_SERVER['HTTP_REFERER'] ?? '?url=dashboard';
        $this->redirect($referer);
    }
}
