<?php
 $notifications = auth()->user() ?
  auth()->user()->unreadNotifications()->select('notifications.*')->where('type', 'App\Notifications\AdminOrder')->take(5)->get() :
   []; ?>
        <li class="scrollable-container media-list" id="loadNotifications">
            @forelse($notifications as $key => $notification)
                    <a class="d-flex justify-content-between" href="{{route('orders.show', $notification->data['admin_order_id'])}}">
                        <div class="media d-flex align-items-center">
                            <div class="media-left pr-0">
                                <div class="avatar mr-1 m-0">
                                    <img src="{{env('APP_URL').config('constants.notify').'placed.png'}}"
                                         alt="avatar"
                                         height="39"
                                         width="39"/>
                                </div>
                            </div>
                            <div class="media-body">
                                <h6 class="media-heading">
                                    <span class="text-bold-500">New Order Arrived!</span><br>
                                    {{$notification->data['order_referel'] ?? null}}
                                </h6>
                                <small class="notification-text">{{\Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans()}}</small>
                            </div>
                        </div></a>
            @empty
                <h5 class="text-center px-auto py-3">No notifications</h5>
            @endforelse
        </li>

