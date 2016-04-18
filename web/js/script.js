$(function () {
    $('.editor-modal').each(function () {
        var self = $(this),
            check = $('.btn-toolbar').append('<label class="btn btn-default"><input type="checkbox" class="edit-all" style="margin-right: 4px;">Edit selected together</label>').find('.edit-all');
        self.on('show.bs.modal', function (e) {
            if (check.prop('checked')) {
                var texts = self.find(':text'),
                    textareas = self.find('textarea');
                texts.on('change', function () {
                    texts.val($(this).val());
                });
                textareas.on('change', function () {
                    textareas.val($(this).val());
                });
            }
        });
    });
    $('.page-gallery').each(function () {
        var page = $(this),
            title = page.find('.title'),
            desc = page.find('.desc');
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
                            desc.css({
                                'opacity': 1,
                                'pointer-events': 'all'
                            });
                        });
                    }
                    else {
                        ul.slideDown(150, function () {
                            animated = false;
                            hideEventOn();
                        });
                        desc.css({
                            'opacity': 0,
                            'pointer-events': 'none'
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
        desc.each(function () {
            var self = $(this),
                animate = false;
                a = self.children('a'),
                p = self.children('p'),
                h = p.height()+1,
                w = p.width()+1,
                init = function () {
                    $(document).on('click.gallerydesc keydown.gallerydesc', function () {
                        animate = true;
                        $(document).off('.gallerydesc');
                        p.animate({
                            'height': a.height(),
                            'width': a.width(),
                            'opacity': 0
                        }, 150, function () {
                            // p.css('display', 'none');
                        });
                        a.css('display', 'block');
                        a.animate({
                            'opacity': 1
                        }, 150, function () {
                            self.addClass('lowdesc');
                            animate = false;
                        });
                    });
                };
            if (a.length > 0) {
                init();
                a.on('click', function (e) {
                    animate = true;
                    e.preventDefault();
                    self.removeClass('lowdesc');
                    // p.css('display', 'block');
                    p.animate({
                        'height': h,
                        'width': w,
                        'opacity': 1
                    }, 150);
                    a.animate({
                        'opacity': 0
                    }, 150, function () {
                        a.css('display', 'none');
                        animate = false;
                        init();
                    });
                }).css('display', 'none');
            }
        }).css({
            opacity: 0
        });
        setTimeout(function () {
            var fotorama = page.find('.fotorama'),
                fotoobj = fotorama.data('fotorama'),
                stage = fotorama.find('.fotorama__stage'),
                nav = fotorama.find('.fotorama__nav'),
                heart = stage.after('<div class="heart"><a><sapn class="glyphicon glyphicon-heart-empty"></sapn></a></div>').next(),
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
            stage.after(title, desc);
            if (nav.length > 0) {
                var btm = nav.height();
                heart.add(desc).css({
                    'margin-bottom': btm
                });
                desc.animate({
                    opacity: 1
                }, 150);
            }
            else {
                desc.css({
                    opacity: 1
                });
            }
            // fotorama.find('.fotorama__caption').addClass('no-events');
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
                    heart.addClass('noheart');
                }
            }).on('mouseup touchend', function () {
                downed = grabbing = false;
                heart.removeClass('noheart');
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