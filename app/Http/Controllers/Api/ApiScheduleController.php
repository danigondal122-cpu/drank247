<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Schedule;
use App\Models\ScheduleAbsense;
use Illuminate\Http\Request;

class ApiScheduleController extends Controller
{
  public function updateScheduleStatus(Request $request)
  {

    $token = $request->input('token');
    $id = $request->input('id');
    $schedule_id = $request->input('schedule_id');
    $type = $request->input('type');

    if ($type == 'Approve') {
      $status = '2';
      Schedule::where('s_id', $schedule_id)->update(['s_status' => $status, 's_approvedtime' => now()]);

      return response()->json(['status' => true, 'message' => 'Schedule Approved!!']);
    }
    if ($type == 'Reject') {
      $status = '7';

      $find = Schedule::where('s_id', $schedule_id)->first();
      $currenttime = strtotime(date('Y-m-d H:i:s'));
      $stop_time = strtotime(date('Y-m-d H:i:s', strtotime($find['s_approvedtime'] . ' +1 day')));

      if ($find['s_approvedtime'] == "" || $currenttime <= $stop_time) {
        Schedule::where('s_id', $schedule_id)->update(['s_status' => $status]);
        return response()->json(['status' => true, 'message' => 'Schedule Rejected!!']);
      } else {
        return response()->json(['status' => false, 'message' => 'You can not reject this shedule now,please contact to Franchises']);
      }
    }
  }

  public function RequestSchedulelist(Request $request)
  {

    $token = $request->input('token');
    $dp_id = $request->input('id');

    $data['schedule'] = Schedule::where('s_dpid', $dp_id)->leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'schedule.s_fid');
    })->leftJoin('pools', function ($join) {
      $join->on('pools.pool_id', '=', 'schedule.s_pool');
    })->join('order_status', 'order_status.os_id', 'schedule.s_status')->get(['s_id', 'franchises_name', 's_time', 's_startdate', 's_enddate', 'area', 'os_name']);

    foreach ($data['schedule'] as $key => $value) {
      $sdate = strtotime($value['s_startdate']); // get unix timestamp
      $startdate = $sdate * 1000;
      $edate = strtotime($value['s_enddate']); // get unix timestamp
      $enddate = $edate * 1000;
      $data['schedule'][$key]['s_starttime'] = date("H:i:s", strtotime($value['s_startdate']));
      $data['schedule'][$key]['s_endtime'] = date("H:i:s", strtotime($value['s_enddate']));
      $data['schedule'][$key]['s_startdate'] = $startdate;
      $data['schedule'][$key]['s_enddate'] = $enddate;
    }

    return response()->json(['status' => true, 'data' => $data['schedule']]);
  }
  public function ApproveSchedulelist(Request $request)
  {

    $token = $request->input('token');
    $dp_id = $request->input('id');

    $data['schedule'] = Schedule::where('s_dpid', $dp_id)->where('s_status', '2')->leftJoin('franchises', function ($join) {
      $join->on('franchises.id', '=', 'schedule.s_fid');
    })->leftJoin('pools', function ($join) {
      $join->on('pools.pool_id', '=', 'schedule.s_pool');
    })->join('order_status', 'order_status.os_id', 'schedule.s_status')->get(['s_id', 'franchises_name', 's_time', 's_startdate', 's_enddate', 'area', 'os_name', 's_approvedtime']);
    foreach ($data['schedule'] as $key => $value) {
      $stop_time = strtotime(date('Y-m-d H:i:s', strtotime($value['s_approvedtime'] . ' +1 day')));
      $currenttime = strtotime(date('Y-m-d H:i:s'));
      $time = $stop_time - $currenttime;
      $remaining_time = date("H:i:s", $time);
      $sdate = strtotime($value['s_startdate']); // get unix timestamp
      $startdate = $sdate * 1000;
      $edate = strtotime($value['s_enddate']); // get unix timestamp
      $enddate = $edate * 1000;
      $s_approvedtime = strtotime($value['s_approvedtime']);
      $s_approvedtime = $s_approvedtime * 1000;
      $data['schedule'][$key]['s_starttime'] = date("H:i:s", strtotime($value['s_startdate']));
      $data['schedule'][$key]['s_endtime'] = date("H:i:s", strtotime($value['s_enddate']));
      $data['schedule'][$key]['s_startdate'] = $startdate;
      $data['schedule'][$key]['s_enddate'] = $enddate;
      $data['schedule'][$key]['remaining_time'] = $remaining_time;
      $data['schedule'][$key]['s_approvedtime'] = ($value['s_approvedtime'] == null) ? 0 : $s_approvedtime;
    }
    $data['absent'] = ScheduleAbsense::where('sa_dpid', $dp_id)->get();
    foreach ($data['absent'] as $key => $value) {
      $sdate = strtotime($value['sa_starttime']); // get unix timestamp
      $startdate = $sdate * 1000;
      $edate = strtotime($value['sa_endtime']); // get unix timestamp
      $enddate = $edate * 1000;
      $data['absent'][$key]['sa_starttime'] = $startdate;
      $data['absent'][$key]['sa_endtime'] = $enddate;
    }

    return response()->json(['status' => true, 'schedule' => $data['schedule'], 'absent' => $data['absent']]);
  }

  public function setAbsencedate(Request $request)
  {

    $token = $request->input('token');
    $dp_id = $request->input('id');
    $start_time = json_decode(stripslashes($request->input('start_time')));

    foreach ($start_time as $key => $value) {

      $schedule = new ScheduleAbsense();
      $schedule->sa_dpid = $dp_id;
      $schedule->sa_starttime = $value->start_time;
      $schedule->sa_endtime = $value->end_time;
      $schedule->save();
    }
    return response()->json(['status' => true, 'message' => 'Absence date added Successfully!!']);
  }

  public function absenceList(Request $request)
  {

    $token = $request->input('token');
    $dp_id = $request->input('id');

    $data['schedule'] = ScheduleAbsense::where('sa_dpid', $dp_id)->get(['s_abid', 'sa_starttime', 'sa_endtime']);


    foreach ($data['schedule'] as $key => $value) {
      $sdate = strtotime($value['sa_starttime']); // get unix timestamp
      $startdate = $sdate * 1000;
      $edate = strtotime($value['sa_endtime']); // get unix timestamp
      $enddate = $edate * 1000;
      $data['schedule'][$key]['sa_starttime'] = $startdate;
      $data['schedule'][$key]['sa_endtime'] = $enddate;
    }

    return response()->json(['status' => true, 'data' => $data['schedule']]);
  }
  public function deleteAbsence(Request $request)
  {

    $token = $request->input('token');
    $dp_id = $request->input('id');
    $s_abid = $request->input('s_abid');

    ScheduleAbsense::where('s_abid', $request->s_abid)->delete();

    return response()->json(['status' => true, 'message' => 'Deleted Successfully!!']);
  }
}
