<nav class="header__nav">
    <ul>
        <li><a href="/admin/attendance/list">勤怠一覧</a></li>
        <li><a href="/admin/staff/list/">スタッフ一覧</a></li>
        <li><a href="/admin/stamp_correction_request/list">申請一覧</a></li>
        <li>
            <form action="/logout" method="POST">
                @csrf
                <button class="header__logout">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
