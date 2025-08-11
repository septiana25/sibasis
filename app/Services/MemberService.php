<?php
namespace App\Services;

use App\Rules\ValidMember;
use Illuminate\Http\Request;

class MemberService{
  
  public function getMemberAktif(Request $request){
    $rule = new ValidMember();

    $request->validate([
        'anggota' => ['required', 'numeric', $rule],
    ]);

    $member = $rule->getMember();

    // Safety check (seharusnya tidak null karena rule sudah valid)
    if (!$member) {
        return redirect()->route('member.login')->with(['member_id' => 'Terjadi kesalahan, silakan coba lagi.']);
    }
    return $member;
  }
}