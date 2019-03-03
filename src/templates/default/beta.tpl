<style>
.flag {
    position: relative;
    margin: 15px;
    display: inline-block;
    width: 36px;
    height: 40px;
    color: rgba(0, 0, 0, 0.55);
    font-size: 32px;
    text-shadow: rgba(0, 0, 0, 0.05) 1px 1px 0;
    font-weight: bold;
    filter: drop-shadow(rgba(0, 0, 0, 0.45) 1px 2px 5px);
}

.flag > .helper {
    position: absolute;
    top: 0;
    left: -3px;
    right: -3px;
    bottom: 0;
    background: linear-gradient(to bottom, #00b3f8 0%, #008ddb 100%);
    z-index: -1;
}

.flag > .content {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    position: relative;
    background: linear-gradient(to bottom, #00b3f8 0%, #008ddb 100%);
    filter: drop-shadow(#fff 0 0 1px);
}

.flag > .helper:after {
    content: '';
    position: absolute;
    left: 0;
    top: 100%;
    border-left: 21px solid transparent;
    border-right: 21px solid transparent;
    border-top: 18px solid #008ddb;
}

.flag > .content:after {
    content: '';
    position: absolute;
    left: 0;
    top: 100%;
    border-left: 18px solid transparent;
    border-right: 18px solid transparent;
    border-top: 15px solid #008ddb;
}
</style>
<div class="flag">
    <span class="helper"></span>
    <span class="content">α</span>
</div>
<div class="flag">
    <span class="helper"></span>
    <span class="content">β</span>
</div>
<div class="flag">
    <span class="helper"></span>
    <span class="content">γ</span>
</div>
<div class="flag">
    <span class="helper"></span>
    <span class="content">λ</span>
</div>