$(function () {

    $('.page-gallery').each(function () {
        var page = $(this),
            title = page.find('.title');
        title.find('h1 a').each(function () {
            var self = $(this),
                ul = title.children('ul'),
                visibled = ul.is(':visible'),
                animated = false,
                hideEventOn = function () {
                    $(document).on('click.gallery', function () {
                        if ($(this).closest(title).length === 0) {
                            self.triggerHandler('click');
                        }
                    }).on('keyup.gallery', function (e) {
                        if (e.keyCode == 27) {
                            self.triggerHandler('click');
                        }
                    });
                },
                hideEventOff = function () {
                    $(document).off('.gallery');
                };
            self.on('click', function () {
                if (!animated) {
                    animated = true;
                    if (visibled) {
                        hideEventOff();
                        ul.slideUp(150, function () {
                            animated = false;
                        });
                    }
                    else {
                        ul.slideDown(150, function () {
                            animated = false;
                            hideEventOn();
                        });
                    }
                    visibled = !visibled;
                }
            });
            if (location.hash === '#menu') {
                setTimeout(function () {
                    self.triggerHandler('click');
                }, 300);
            }
        });
        setTimeout(function () {
            var fotorama = page.find('.fotorama'),
                fotoobj = fotorama.data('fotorama'),
                stage = fotorama.find('.fotorama__stage'),
                heart = fotorama.children('.fotorama__wrap').append('<div class="heart"><a><sapn class="glyphicon glyphicon-heart-empty"></sapn></a></div>').children(':last-child'),
                link = heart.children('a'),
                icon = link.children('.glyphicon'),
                cnt = link.append('<span class="cnt"></span>').children(':last-child'),
                liked, count, index, id,
                onShow = function () {
                    id = fotoobj.data[fotoobj.activeIndex]['id'];
                    liked = (index = yii.liked.indexOf(id)) != -1;
                    count = liked ? 1 : 0;
                    if (yii.like[id]) {
                        count += yii.like[id];
                    }
                    show();
                },
                show = function() {
                    link.toggleClass('liked', liked);
                    icon.toggleClass('glyphicon-heart', liked);
                    icon.toggleClass('glyphicon-heart-empty', !liked);
                    cnt.html(count);
                };
            link.on('click', function () {
                if (link.css('opacity') < 1) {
                    return false;
                }
                if (liked = !liked) {
                    index = yii.liked.push(id) - 1;
                    count++;
                }
                else {
                    delete yii.liked[index];
                    count--;
                }
                $.post(yii.likedUrl, {
                    'id': id,
                    'liked': liked
                });
                show();
            });
            var grabbing = false,
                downed = false;
            stage.on('mousedown touchstart', function () {
                downed = true;
            }).on('mousemove touchmove', function () {
                if (downed && !grabbing) {
                    grabbing = true;
                    // heart.addClass('noheart');
                    heart.fadeOut(150);
                }
            }).on('mouseup touchend', function () {
                downed = grabbing = false;
                // heart.removeClass('noheart');
                heart.fadeIn(150);
            }).on('mouseenter', function () {
                heart.removeClass('lowheart');
            }).on('mouseleave', function () {
                heart.addClass('lowheart');
            });
            fotorama
                .on('fotorama:showend', onShow)
                .on('fotorama:show fotorama:showend', function (e, fotorama, extra) {
                    // console.log(e);
                    // console.log(fotorama);
                    // console.log(extra);
                    heart.toggleClass('noheart', e.type === 'fotorama:show');
                    // console.log(e.type + (extra.user ? ' after user’s touch' : ''));
                    // console.log('transition duration: ' + extra.time);
                });
            onShow();
        }, 1);
    });
});