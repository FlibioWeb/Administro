<?php

    namespace Administro\Form;

    use \Administro\Administro;

    class FormManager {

        private $forms;

        public function __construct() {
            $this->forms = array(new ParseForm, new SavePageForm, new UploadForm, new LoginForm, new UpdateForm, new CacheForm);
        }

        // Registers a new route.
        public function registerForm($form) {
            if($form instanceof Form) {
                array_push($this->forms, $form);
                return true;
            }
            return false;
        }

        // Processes the form
        public function processForm($id, $post) {
            // Check if the form id exists
            foreach ($this->forms as $form) {
                // Check if the id matches the form id
                if($form->getId() == $id) {
                    // Process the form
                    return $form->process($post);
                } else {
                    continue;
                }
            }
        }

    }
