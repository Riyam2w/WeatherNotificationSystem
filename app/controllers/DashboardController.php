<?php

declare(strict_types=1);

class DashboardController extends Controller {
     private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    } 

public function index(): void
{
    Auth::requireLogin();

    $userId = Auth::id();

    $alertModel = new Alert($this->conn);
    $subModel   = new Subscription($this->conn);
    $history    = new AlertHistory($this->conn);

    // ✅ GET USER HERE (NOT IN VIEW)
    $user = Auth::user($this->conn);

    $this->view('dashboard/index', [
        'user' => $user,
        'stats' => [
            'cities' => $alertModel->countCities($userId),
            'active' => $alertModel->countActive($userId),
            'today'  => $history->countToday($userId),
            'last'   => $history->lastSentTime($userId),
        ],
        'subscription' => $subModel->current($userId),
        'alerts'       => $alertModel->all($userId),
        'history'      => $history->recent($userId),
    ]);
}

    }

