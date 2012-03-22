<?php
class SubscriptionsController extends AppController {

	var $name = 'Subscriptions';

	function index() {
		$this->Subscription->recursive = 0;
		$this->set('subscriptions', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid subscription'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('subscription', $this->Subscription->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Subscription->create();
			if ($this->Subscription->save($this->data)) {
				$this->Session->setFlash(__('The subscription has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The subscription could not be saved. Please, try again.'));
			}
		}
		$groups = $this->Subscription->Group->find('list');
		$this->set(compact('groups'));
	}

    function unsubscribe($ref) {
        $this->Subscription->deleteAll(array("Subscription.ref" => $ref));
    }

    function subscribe() {
        $this->layout = "ajax";
        $errors = array();
        $i18n_errors = array();
        $message = null;

		if (!empty($this->data)) {
			$this->Subscription->create();
            $this->data["Subscription"]["ref"] = String::uuid();
			if ($this->Subscription->save($this->data)) {
				$message = __('Thanks for subscribing!');
			} else {
                $errors = $this->Subscription->invalidFields();
                foreach ($errors as $error_key=>$error_message) {
                    $i18n_errors[$error_key] = __($error_message);
                }
			}

		}

        $data = array("errors" => sizeof($i18n_errors) > 0 ? $i18n_errors : false);
        if ($message != null) {
            $data["message"] = $message;
        }

        $this->set("data", $data); 

        $this->render("json");
    }

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid subscription'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Subscription->save($this->data)) {
				$this->Session->setFlash(__('The subscription has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The subscription could not be saved. Please, try again.'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Subscription->read(null, $id);
		}
		$groups = $this->Subscription->Group->find('list');
		$this->set(compact('groups'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for subscription'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Subscription->delete($id)) {
			$this->Session->setFlash(__('Subscription deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Subscription was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
?>