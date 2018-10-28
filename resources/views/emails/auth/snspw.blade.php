<div style="margin: 50px;">
    <h2 style="color: #265392;">인하컬쳐라인 비밀번호 찾기</h2>
    <span><strong>{{ $user->name }}</strong>님이 요청하신 계정은 SNS 로그인 계정입니다.</span>

    <p><strong>이메일: </strong> {{ $user->email }}</p>
    <p><strong>SNS 계정: </strong> {{ $user->provider }}</p>
</div>