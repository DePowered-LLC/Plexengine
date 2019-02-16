<div id="my_profile" class="tabs">
    <div class="caption">
        <span tab-id="vip"><b style="color: #fabc1b">VIP</b></span>
        <span tab-id="profile" class="active">| profile |</span>
        <span tab-id="security">| security |</span>
        <span tab-id="datings">| datings |</span>
        <span tab-id="photo">| photo |</span>
        <span tab-id="video">| video |</span>
        <span tab-id="remove_profile">| rem_profile |</span>
    </div>
    <form tab-id="profile" class="tab flex active">
        <div style="position: relative; flex: 1;">
            <h2>Обо мне</h2>
            <p edit="info">Кря</p>
            <table>
                <tr>
                    <td>Дата рождения:</td>
                    <td>23.04.1940</td>
                </tr>
                <tr>
                    <td>Город:</td>
                    <td edit="city">Саратов</td>
                </tr>
                <tr>
                    <td>Семейное положение:</td>
                    <td edit="family_status">Не указано</td>
                </tr>
                <tr>
                    <td>Место работы:</td>
                    <td edit="work">Не указано</td>
                </tr>
                <tr>
                    <td>Веб-сайт:</td>
                    <td edit="site">Не указано</td>
                </tr>
            </table>
            <br>
            <button style="display: none;" class="btn" type="submit">Применить</button>
            <h2>Контакты</h2>
        </div>
        
    </form>
    <div class="tab" tab-id="datings">
        <h2>| datings |</h2>
        <table>
            <tr>
                <td>Познакомлюсь:</td>
                <td>с Чёрной Дырой<br />в возрасте 5-7 млн. лет</td>
            </tr>
            <tr>
                <td>Цель знакомства:</td>
                <td edit="city">Захватить вселенную</td>
            </tr>
            <tr>
                <td>Семейное положение:</td>
                <td edit="family_status">В активном поиске проблем</td>
            </tr>
            <tr>
                <td>Материальная поддержка:</td>
                <td edit="work">никому не помешает</td>
            </tr>
        </table>
        <h2>Интересы</h2>
        <span class="tag">Просто</span>
        <span class="tag">куча</span>
        <span class="tag">интересов</span>
        <span class="tag">которые</span>
        <span class="tag">никому</span>
        <span class="tag">не</span>
        <span class="tag">будут</span>
        <span class="tag">никогда</span>
        <span class="tag">интересны</span>
    </div>
    <form id="profile_contacts" tab-id="contacts" class="tab">
        <div>
            <span edit="vk"><img src="/public/img/icons/vk.png"> @dima10z</span>
            <span edit="phone"><img src="/public/img/icons/phone.png"> Не указано</span>
            <span edit="skype"><img src="/public/img/icons/skype.png"> Не указано</span>
            <span edit="inst"><img src="/public/img/icons/inst.png"> Не указано</span>
        </div>
    </form>
    <div tab-id="photos" class="tab"></div>
</div>

<style>
#my_profile {
    max-height: 475px;
    overflow: hidden auto;
    overflow-y: overlay;
}

#my_profile h2 {
    color: #63a2dd;
    text-align: left;
}

#my_profile td { vertical-align: top; }
#my_profile td:first-child { color: #8d8d8d; }
#my_profile table {
    margin: 0;
    width: 100%;
    font-size: 14px;
}
</style>