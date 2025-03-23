<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\AdminDashboardReposity;

class DashboardService
{
    public function __construct(protected AdminDashboardReposity $dashboardRepo){
    }

    public function getAllAdmins($request)
    {
        $limit = $this->getLimit($request);
        return $this->dashboardRepo->getAllAdmins($limit);
    }

    public function getAllStudents($request)
    {
        $limit = $this->getLimit($request);
        return $this->dashboardRepo->getAllStudents($limit);
    }

    public function getAllInstructors($request)
    {
        $limit = $this->getLimit($request);
        return $this->dashboardRepo->getAllInstructors($limit);
    }

    public function getCourses( $request)
    {
        $limit = $this->getLimit($request);
        return $this->dashboardRepo->getCourses($limit);
    }

    public function getStudentsFromCourse(int $id,  $request)
    {
        $limit = $this->getLimit($request);
        $isComplete = $request->has('is_completed') ? filter_var($request->is_completed, FILTER_VALIDATE_BOOLEAN) : null;
        return $this->dashboardRepo->getStudentsFromCourse($id, $limit, $isComplete);
    }

    private function getLimit(Request $request)
    {
        return ($request->has('limit') && is_numeric($request->limit) <= 100) ? (int) $request->limit : 20;
    }
}
