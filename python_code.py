# Решение тестовой задачи на Python

import re


def slash_search(s, ind=[]):
    # функция для поиска индексов расположения "\" в строке

    if (len(ind) > 0) and (ind[-1] != len(ind)-1):
        search_index = ind[-1] + 1
    else:
        search_index = 0

    res = s.find('\\', search_index)

    if res == -1:
        return ind
    else:
        ind.append(res)
        return slash_search(s, ind)


def string_extend(string):
    # функция расширения строки

    if string == "" or string.isalpha():
        return string
    elif string.isdigit():
        raise ValueError("Несоответствующее значение")
    else:
        slash_indices = slash_search(string)

        new_string = ''
        start_ind = 0
        for m in re.finditer(r'\d', string):
            ind = m.start()
            if ind-1 in slash_indices:
                if ind-2 not in slash_indices:
                    print(ind)
                    new_string += string[start_ind:ind-1]
                    start_ind = ind
                    print(new_string)
                elif ind-2 in slash_indices:
                    new_string += string[start_ind:ind-1] + string[ind-1] * (int(m[0])-1)
                    start_ind = ind + 1
            else:
                if string[ind-1] and ind-1 >= 0:
                    new_string += string[start_ind:ind] + string[ind-1] * (int(m[0])-1)
                    start_ind = ind + 1

        if start_ind < len(string):
            new_string += string[start_ind:]

        return new_string


if __name__ == "__main__":
    # test_string = "v4bc3d5e"
    # assert string_extend(test_string) == "vvvvbcccddddde"
    #
    # test_string = "abcd"
    # assert string_extend(test_string) == "abcd"
    #
    # test_string = "45"
    # # assert string_extend(test_string) == ""      # raised exception
    #
    # test_string = ""
    # assert string_extend(test_string) == ""
    #
    test_string = r"qwe\4\5"
    assert string_extend(test_string) == r"qwe45"
    #
    # test_string = r"qwe\45"
    # assert string_extend(test_string) == r"qwe44444"

    # test_string = r"qwe\\5 "
    # assert string_extend(test_string) == r"qwe\\\\\ "

    # print(string_extend(test_string))
