<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return
            [
                'id'                            =>  $this->id,
                'date_hired'                    =>  $this->date_hired,
                'username'                      =>  $this->username,
                'fname'                         =>  $this->fname,
                'lname'                         =>  $this->lname,
                'email'                         =>  $this->email,
                'contact'                       =>  $this->contact,
                'emp_id'                        =>  $this->emp_id,
                'signature'                     =>  $this->signature,
                'requestSignatureReset'         =>  $this->requestSignatureReset,
                'approvedSignatureReset'        =>  $this->approvedSignatureReset,
                'notification_counts'           =>  $this->notification_counts,

                'roles'                         => $this->whenLoaded('roles',
                                                    fn() => $this->roles->map(
                                                        fn($r) =>
                                                            [
                                                                'id'    =>  $r->id,
                                                                'name'  =>  $r->name
                                                            ]
                                                    )
                                                ),
                'departments'                   => $this->whenLoaded('departments',
                                                    fn() =>
                                                        [
                                                            'id'                =>  $this->departments->id,
                                                            'department_name'   =>  $this->departments->department_name
                                                        ]
                                                ),
                'branch'                        => $this->whenLoaded('branch',
                                                    fn() =>
                                                        [
                                                            'id'            =>  $this->branch->id,
                                                            'branch_code'   =>  $this->branch->branch_code,
                                                            'branch_name'   =>  $this->branch->branch_name
                                                        ]
                                                ),
                'branches'                      => $this->whenLoaded('branches',
                                                    fn() => $this->branches->map(
                                                        fn($r) =>
                                                            [
                                                                'id'=>$r->id,
                                                                'branch_code'   =>  $r->branch_code,
                                                                'branch_name'   =>  $r->branch_name
                                                            ]
                                                    )
                                                ),
                'positions'                     => $this->whenLoaded('positions',
                                                    fn() =>
                                                        [
                                                            'id'        =>  $this->positions->id,
                                                            'label'     =>  $this->positions->label
                                                        ]
                                                ),
                'notifications'                 => $this->whenLoaded('notifications',
                                                    fn() => $this->notifications->map(
                                                        fn($r) =>
                                                            [
                                                                'id'            =>  $r->id,
                                                                'data'          =>  $r->data,
                                                                'created_at'    =>  $r->created_at
                                                            ]
                                                    )
                                                )
            ];
    }
}
