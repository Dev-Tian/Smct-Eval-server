<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adaptability whereUsersEvaluationId($value)
 */
	class Adaptability extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $branch_code
 * @property string $branch_name
 * @property string $branch
 * @property string $acronym
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereAcronym($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereBranchCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereBranchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereUpdatedAt($value)
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $explanation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerService whereUsersEvaluationId($value)
 */
	class CustomerService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $department_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $explanation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ethical whereUsersEvaluationId($value)
 */
	class Ethical extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobKnowledge whereUsersEvaluationId($value)
 */
	class JobKnowledge extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $label
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereValue($value)
 */
	class Position extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QualityOfWork whereUsersEvaluationId($value)
 */
	class QualityOfWork extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UsersEvaluation> $usersEvaluations
 * @property-read int|null $users_evaluations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuarterUsersEvaluation whereUpdatedAt($value)
 */
	class QuarterUsersEvaluation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reliability whereUsersEvaluationId($value)
 */
	class Reliability extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $reason
 * @property int $days
 * @property string $suspended_by
 * @property int $is_done
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $users
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereSuspendedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suspension whereUserId($value)
 */
	class Suspension extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $users_evaluation_id
 * @property int $question_number
 * @property int $score
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersEvaluation $usersEvaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereQuestionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teamwork whereUsersEvaluationId($value)
 */
	class Teamwork extends \Eloquent {}
}

namespace App\Models{
/**
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method void assignRole(...$roles)
 * @property int $id
 * @property int|null $position_id
 * @property int|null $department_id
 * @property string $username
 * @property string $fname
 * @property string $lname
 * @property string $email
 * @property string $password
 * @property string $contact
 * @property string $emp_id
 * @property string $is_active
 * @property string|null $date_hired
 * @property string|null $employeeSignatureDate
 * @property string|null $signature
 * @property int $reinstated
 * @property string|null $reinstated_date
 * @property int $suspension
 * @property string|null $avatar
 * @property string|null $bio
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \App\Models\Department|null $departments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UsersEvaluation> $doesEvaluated
 * @property-read int|null $does_evaluated_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UsersEvaluation> $evaluations
 * @property-read int|null $evaluations_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $positions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Suspension> $suspensions
 * @property-read int|null $suspensions_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User search($term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateHired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmployeeSignatureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReinstated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReinstatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $employee_id
 * @property int|null $evaluator_id
 * @property int|null $quarter_of_submission_id
 * @property string $category
 * @property int $rating
 * @property string $status
 * @property int $reviewTypeProbationary
 * @property int $reviewTypeOthersImprovement
 * @property string $reviewTypeOthersCustom
 * @property string $priorityArea1
 * @property string $priorityArea2
 * @property string $priorityArea3
 * @property string $remarks
 * @property string $overallComments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Adaptability> $adaptabilities
 * @property-read int|null $adaptabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerService> $customerServices
 * @property-read int|null $customer_services_count
 * @property-read \App\Models\User|null $employee
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ethical> $ethicals
 * @property-read int|null $ethicals_count
 * @property-read \App\Models\User|null $evaluator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobKnowledge> $jobKnowledge
 * @property-read int|null $job_knowledge_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualityOfWork> $qualityOfWorks
 * @property-read int|null $quality_of_works_count
 * @property-read \App\Models\QuarterUsersEvaluation|null $quarterUsersEvaluations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reliability> $reliabilities
 * @property-read int|null $reliabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teamwork> $teamworks
 * @property-read int|null $teamworks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereEvaluatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereOverallComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation wherePriorityArea1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation wherePriorityArea2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation wherePriorityArea3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereQuarterOfSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereReviewTypeOthersCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereReviewTypeOthersImprovement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereReviewTypeProbationary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersEvaluation whereUpdatedAt($value)
 */
	class UsersEvaluation extends \Eloquent {}
}

