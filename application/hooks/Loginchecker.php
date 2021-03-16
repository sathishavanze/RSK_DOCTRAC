<?php

class Loginchecker
{
    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->helper('url');
        $this->CI->load->library('ACL'); // If not loaded, then load it here

        if (!isset($this->CI->session)) { // Check if session lib is loaded or not
            $this->CI->load->library('session'); // If not loaded, then load it here
        }
    }

    function loginCheck()
    {
        $controller = $this->CI->uri->rsegment(1);
        $action = $this->CI->uri->rsegment(2);

        if ($this->CI->session->userdata('UserUID')) {
            // Check for ACL
            if (!$this->CI->acl->hasAccess()) {
                if ($controller != 'Dashboard' && !in_array($controller . '/' . $action, $this->CI->acl->getGuestPages())) {
                    if ($this->CI->input->is_ajax_request()) {
                        ?>
                        <script>
						window.location.href='<?php echo base_url(); ?>';
                        </script>
                        <?php
                        exit;
                    } else {
                        return redirect('/Dashboard');
                    }
                    // $message_403 = "You don't have access to the url you where trying to reach.";
                    // show_error($message_403, 403);
                }
            }
        } else {
            if ($controller != 'Login' && !in_array($controller . '/' . $action, $this->CI->acl->getGuestPages())) {
                return redirect('/Login');
            }
        }
    }
}

?>