<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\Exam\SelectQuestion;
use App\Models\Exam\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(request $request, Subject $subject = null): View
    {
        $data['questions'] = $subject
            ? $subject->questions()->inRandomOrder()->limit(20)->get()
            : SelectQuestion::inRandomOrder()->limit(20)->get();

        return view('exam.exam', $data);
    }

    public function store(request $request): View
    {
        // 作答答案
        $ans = $request->input('option');

        // 題目 id
        $ids = array_keys($ans);

        // 取得題目
        $questions = SelectQuestion::whereIn('id', $ids)->get()->keyBy('id');

        // 分數及錯誤答案
        $data['grade'] = 0;
        $data['results'] = [];

        foreach ($ans as $key => $val) {
            if ($questions[$key]->answer === $val) {
                $data['grade'] += 5;
            } else {
                $data['results'][] = $questions[$key];
            }
        }

        return view('exam.result', $data);
    }
}
