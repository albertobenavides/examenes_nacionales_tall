<?php

namespace App\Observers;

use App\Models\Meeting;
use BigBlueButton\Parameters\CreateMeetingParameters;

class MeetingObserver
{
    /**
     * Handle the Meeting "created" event.
     */
    public function created(Meeting $meeting): void
    {
        \Bigbluebutton::create([
            'meetingID' => $meeting->id,
            'meetingName' => $meeting->meetingName,
            'attendeePW' => 'attendee',
            'moderatorPW' => 'moderator',
            'record' => true,
            'endCallbackUrl'  => env('APP_URL') . 'meeting/' . $meeting->id . '/terminada',
            'bbb-recording-ready-url'  => env('APP_URL') . 'meeting/' . $meeting->id . '/grabada',
            'logoutUrl' => env('APP_URL') . 'admin/cursos/' . $meeting->curso_id . '/edit?activeRelationManager=2',

            'lockSettingsDisableCam' => true,
            'lockSettingsDisableMic' => true,
            'lockSettingsDisablePrivateChat' => true,
            'lockSettingsDisableNotes' => true,
            'lockSettingsLockOnJoinConfigurable' => true,
            'guestPolicy' => 'ALWAYS_DENY',
            'endWhenNoModerator' => true,
            'disabledFeatures' => 'sharedNotes'

        ]); 
        // $meetingParams = new CreateMeetingParameters($meeting->id, $meeting->meetingName);
        // $meetingParams->setAttendeePW('attendee');
        // $meetingParams->setModeratorPW('moderator');
        // $meetingParams->setRecord(true);
        // $meetingParams->setEndCallbackUrl(env('APP_URL') . 'meeting/' . $meeting->id . '/terminada');
        // $meetingParams->setRecordingReadyCallbackUrl(env('APP_URL') . 'meeting/' . $meeting->id . '/grabada');
        // $meetingParams->setLogoutURL(env('APP_URL') . 'admin/cursos/' . $meeting->curso_id . '/edit?activeRelationManager=2');
        // \Bigbluebutton::create($meetingParams);
    }

    /**
     * Handle the Meeting "updated" event.
     */
    public function updated(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the Meeting "deleted" event.
     */
    public function deleted(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the Meeting "restored" event.
     */
    public function restored(Meeting $meeting): void
    {
        //
    }

    /**
     * Handle the Meeting "force deleted" event.
     */
    public function forceDeleted(Meeting $meeting): void
    {
        //
    }
}
