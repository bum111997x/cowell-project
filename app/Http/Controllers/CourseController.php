<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\LessonHistory;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        $categories = CourseCategory::with('childs')->where('parent_id', '=', 0)->get();
        $courses = Course::where('status', 1)
                            ->findByName($request)
                            ->findByCategory($request)
                            ->paginate(8);
        return view('screens.frontend.course-list', compact('courses', 'categories'));
    }
    
    public function show($id, $slug)
    {
        $course = Course::where('id', $id)
                        ->where('slug', $slug)
                        ->with(['users', 
                                'sections' => fn($q) => $q->with([
                                                'lessons' => fn($q) => $q->orderBy('order')])
                                                ->orderBy('order')])
                        ->first(); 
        return view('screens.frontend.course-detail', compact('course'));
    }

    public function learn($id, $slug, $lesson = 0)
    {
        $course = Course::where('id', $id)
                        ->where('slug', $slug)
                        ->with(['users', 
                                'sections' => fn($q) => $q->with([
                                                'lessons' => fn($q) => $q->orderBy('order')])
                                                ->orderBy('order')])
                        ->first();
        $lessonHistories = LessonHistory::where('course_id', $course->id)
                                        ->where('user_id', auth()->user()->id)
                                        ->get();
        if($lesson == 0){
            $lesson = $course->sections->first()->lessons->first();
        } else {
            $lesson = Lesson::find($lesson);
        }
        return view('screens.frontend.course-learning', compact('course', 'lesson', 'lessonHistories'));
    }

}