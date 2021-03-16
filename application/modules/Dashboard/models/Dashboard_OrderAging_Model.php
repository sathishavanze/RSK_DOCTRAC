<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_OrderAging_Model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
    }

    // Aging Total Pending Orders.
    function GetAgingTotalPendingOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_not_in('tOrders.StatusUID', $this->GetNotIn_FilterStatus());
        return $this->db->get()->result();
    }

    // Aging Waiting for Image Orders.
    function GetAging_WaitingForImageOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_in('tOrders.StatusUID', $this->config->item('keywords')['Waiting For Images']);
        return $this->db->get()->result();
    }

    // Aging Stacking Orders.
    function GetAging_StackingOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_in('tOrders.StatusUID', [$this->config->item('keywords')['Stacking In Progress'], $this->config->item('keywords')['Image Received']]);
        return $this->db->get()->result();
    }

    // Aging Exception Orders.
    function GetAging_ExceptionOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_in('tOrders.StatusUID', $this->config->item('ExceptionEnabled'));
        return $this->db->get()->result();
    }

    // Aging Export Orders.
    function GetAging_ExportOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_in('tOrders.StatusUID', $this->config->item('ExportEnabled'));
        return $this->db->get()->result();
    }

    // Aging Review Orders.
    function GetAging_ReviewOrders($fdate, $tdate, $Customer, $Project, $Lender)
    {
        $advancedsearch = [];
        if ($Customer != 'all') {

            $advancedsearch['torders.CustomerUID'] = $Customer;
        }
        if ($Project != 'all') {

            $advancedsearch['torders.ProjectUID'] = $Project;
        }
        if ($Lender != 'all') {

            $advancedsearch['torders.LenderUID'] = $Lender;
        }

        $this->db->select('*');
        $this->db->select('Date(OrderDueDate) AS DueDate, Date(OrderEntryDateTime) As EntryDate', false);
        $this->db->from('tOrders');
        $this->db->where_in($advancedsearch);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)>="'.$fdate.'"', NULL, false);
        $this->db->where('DATE(tOrders.OrderEntryDateTime)<="'.$tdate.'"', NULL, false);
        $this->db->where_in('tOrders.StatusUID', $this->config->item('ReviewEnabled'));
        return $this->db->get()->result();
    }

    /* --- SUPPORTING FUNCTIONS STARTS --- */
    function GetFilterStatus()
    {
      $status[] = $this->config->item('keywords')['NewOrder'];
      $status[] = $this->config->item('keywords')['PrescreenCompleted'];
      $status[] = $this->config->item('keywords')['Pendingdocuments'];
      $status[] = $this->config->item('keywords')['Alldocuments received'];
      $status[] = $this->config->item('keywords')['WorkupCompleted'];
      $status[] = $this->config->item('keywords')['Indexing Exception'];
      $status[] = $this->config->item('keywords')['WaitingforConditional Approval'];
      $status[] = $this->config->item('keywords')['OnHold'];
      $status[] = $this->config->item('keywords')['UnderwriterCompleted'];
      $status[] = $this->config->item('keywords')['SchedulingCompleted'];
      $status[] = $this->config->item('keywords')['ClosingCompleted'];
      $status[] = $this->config->item('keywords')['ClosedandBilled'];
      $status[] = $this->config->item('keywords')['Cancelled'];
      return $status;
    }

    function GetNotIn_FilterStatus(Type $var = null)
    {
      $status[] = $this->config->item('keywords')['ClosingCompleted'];
      $status[] = $this->config->item('keywords')['Cancelled'];
      return $status;
    }
    
    /* --- SUPPORTING FUNCTIONS ENDS --- */

} ?>