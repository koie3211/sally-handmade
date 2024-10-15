<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\Exam\Subject;
use App\Models\SelectQuestion;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        // 科目
        $data['subjects'] = Subject::where('enabled', 1)->get();

        // 年份
        // $data['year'] = SelectQuestion::select('year')->groupBy('year')->get();

        return view('exam.index', $data);
    }
}
