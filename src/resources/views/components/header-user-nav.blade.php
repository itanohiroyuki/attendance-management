<nav class="header__nav">
    <ul>
        <li><a href="/attendance">勤怠</a></li>
        <li><a href="/attendance/list">勤怠一覧</a></li>
        <li><a href="/stamp_correction_request/list">申請</a></li>
        <li>
            <form action="/logout" method="POST">
                @csrf
                <button class="header__logout">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
