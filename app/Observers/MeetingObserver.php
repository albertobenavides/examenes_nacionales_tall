<?php

namespace App\Observers;

use App\Models\Meeting;

class MeetingObserver
{
    /**
     * Handle the Meeting "saved" event.
     */
    public function saved(Meeting $meeting): void
    {
        \Bigbluebutton::create([
            'meetingID' => $meeting->id,
            'meetingName' => $meeting->meetingName,
            'attendeePW' => 'attendee',
            'moderatorPW' => 'moderator',
            'endCallbackUrl'  => env('APP_URL') . 'meeting/' . $meeting->id,
            'logoutUrl' => env('APP_URL') . 'admin/cursos/' . $meeting->curso_id . '/edit?activeRelationManager=2',
        ]);
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
